<?php

namespace TomNiemantsverdriet\MannenweekendBingo\AppAPI;

/**
 * PushNotifier class.
 *
 * Minimal VAPID Web Push implementation. Sends payloadless push messages (a "tickle")
 * to a subscription endpoint, which the service worker turns into a notification. Only
 * the subscription endpoint URL is required for payloadless pushes; the encryption keys
 * are not needed because no payload is sent.
 *
 * The VAPID key pair is generated once and stored in the storage directory.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class PushNotifier
{
    /**
     * @var string The subject that identifies the sender to the push service
     */
    private const string SUBJECT = 'mailto:bingo@mannenweekend.local';

    /**
     * Returns the base64url encoded public application server key that the browser needs
     * to subscribe to push messages.
     * @return string The VAPID public key
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getPublicKey(): string
    {
        $details = openssl_pkey_get_details(openssl_pkey_get_private($this->getPrivateKeyPem()));
        $point = "\x04" . $details['ec']['x'] . $details['ec']['y'];

        return $this->base64UrlEncode($point);
    }

    /**
     * Sends a payloadless push message to the given subscription endpoint.
     * @param string $endpoint The push subscription endpoint URL
     * @return bool True when the push service accepted the message
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function send(string $endpoint): bool
    {
        $parts = parse_url($endpoint);

        if ($parts === false || empty($parts['host'])) {
            return false;
        }

        $audience = $parts['scheme'] . '://' . $parts['host'];
        $jwt = $this->createVapidToken($audience);

        $headers = [
            'Authorization: vapid t=' . $jwt . ', k=' . $this->getPublicKey(),
            'TTL: 43200',
            'Content-Length: 0',
        ];

        $handle = curl_init($endpoint);
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        // Push services respond with 201 Created (or 200/202) when the message is accepted

        return $status >= 200 && $status < 300;
    }

    /**
     * Creates a signed VAPID JWT for the given audience.
     * @param string $audience The origin of the push service
     * @return string The signed JWT
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function createVapidToken(string $audience): string
    {
        $header = $this->base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $payload = $this->base64UrlEncode(json_encode([
            'aud' => $audience,
            'exp' => time() + 43200,
            'sub' => self::SUBJECT,
        ]));

        $signingInput = $header . '.' . $payload;

        openssl_sign($signingInput, $derSignature, $this->getPrivateKeyPem(), OPENSSL_ALGO_SHA256);
        $signature = $this->derToJose($derSignature);

        return $signingInput . '.' . $this->base64UrlEncode($signature);
    }

    /**
     * Converts a DER encoded ECDSA signature to the raw 64 byte (R || S) JOSE format.
     * @param string $der The DER encoded signature
     * @return string The 64 byte raw signature
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function derToJose(string $der): string
    {
        $offset = 2;

        // The DER sequence may use a long-form length byte; skip it when present

        if (ord($der[1]) & 0x80) {
            $offset += ord($der[1]) & 0x7f;
        }

        // Read the R integer

        $offset++;
        $rLength = ord($der[$offset++]);
        $r = substr($der, $offset, $rLength);
        $offset += $rLength;

        // Read the S integer

        $offset++;
        $sLength = ord($der[$offset++]);
        $s = substr($der, $offset, $sLength);

        // Strip leading zero padding and left pad both integers to 32 bytes

        $r = str_pad(ltrim($r, "\x00"), 32, "\x00", STR_PAD_LEFT);
        $s = str_pad(ltrim($s, "\x00"), 32, "\x00", STR_PAD_LEFT);

        return $r . $s;
    }

    /**
     * Returns the VAPID private key in PEM format, generating the key pair on first use.
     * @return string The PEM encoded private key
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function getPrivateKeyPem(): string
    {
        $path = STORAGE_PATH . '/vapid_private.pem';

        if (!file_exists($path)) {
            $key = openssl_pkey_new([
                'private_key_type' => OPENSSL_KEYTYPE_EC,
                'curve_name' => 'prime256v1',
            ]);

            openssl_pkey_export($key, $pem);
            file_put_contents($path, $pem);
        }

        return file_get_contents($path);
    }

    /**
     * Encodes the given binary data as base64url without padding.
     * @param string $data The data to encode
     * @return string The base64url encoded data
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
