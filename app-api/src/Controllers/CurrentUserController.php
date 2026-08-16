<?php

namespace TomNiemantsverdriet\MannenweekendBingo\AppAPI\Controllers;

use Exception;
use TomNiemantsverdriet\MannenweekendBingo\AppAPI\APIController;
use TomNiemantsverdriet\MannenweekendBingo\AppAPI\PushNotifier;
use TomNiemantsverdriet\MannenweekendBingo\ActiveRecords\UserRecord;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\User;

/**
 * CurrentUserController class.
 *
 * API controller that exposes the authenticated participant and lets them manage their
 * push notification subscription.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class CurrentUserController extends APIController
{
    /**
     * Returns the authenticated participant (or null) together with the VAPID public key
     * required to subscribe to push notifications.
     * @return array The current user payload
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(): array
    {
        $user = $this->getCurrentUser();

        return [
            'user' => $user?->getAPIData(),
            'vapid_public_key' => (new PushNotifier())->getPublicKey(),
        ];
    }

    /**
     * Stores the push subscription endpoint URL on the authenticated participant.
     * @return array The updated user payload
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function subscribe(): array
    {
        $user = $this->requireCurrentUser();
        $payload = $this->getRequestPayload();

        if (empty($payload['url'])) {
            throw new Exception('A notification URL is required.');
        }

        $user->setNotificationUrl($payload['url']);
        $user->save();

        return $user->getAPIData();
    }

    /**
     * Clears the push subscription of the authenticated participant.
     * @return array The updated user payload
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function unsubscribe(): array
    {
        $user = $this->requireCurrentUser();

        $user->setNotificationUrl(null);
        $user->save();

        return $user->getAPIData();
    }

    /**
     * Returns the authenticated participant based on the session, or null when not authenticated.
     * @return UserRecord|null The current user
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function getCurrentUser(): ?UserRecord
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (empty($userId)) {
            return null;
        }

        return User::find($userId);
    }

    /**
     * Returns the authenticated participant or throws when nobody is authenticated.
     * @return UserRecord The current user
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function requireCurrentUser(): UserRecord
    {
        $user = $this->getCurrentUser();

        if ($user === null) {
            throw new Exception('You must be authenticated.');
        }

        return $user;
    }
}
