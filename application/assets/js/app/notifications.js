/**
 * Notification helpers
 *
 * Wraps the browser Notification and Push APIs. Registering a subscription returns the
 * push endpoint URL, which is stored server side so other participants can be notified.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */

/**
 * Returns whether the browser supports push notifications
 * @return {boolean} True when supported
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
export function notificationsSupported()
{
    return 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
}

/**
 * Requests notification permission, registers the service worker and subscribes to push
 * @param {string} vapidPublicKey The VAPID public key to subscribe with
 * @return {Promise<string>} The push subscription endpoint URL
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
export async function enableNotifications(vapidPublicKey)
{
    if (!notificationsSupported()) {
        throw new Error('Notificaties worden niet ondersteund in deze browser.');
    }

    let permission = await Notification.requestPermission();

    if (permission !== 'granted') {
        throw new Error('Notificaties zijn niet toegestaan.');
    }

    let registration = await navigator.serviceWorker.register('/application/assets/js/sw.js');
    await waitForActivation(registration);

    let subscription = await registration.pushManager.getSubscription();

    if (subscription === null) {
        subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
        });
    }

    return subscription.endpoint;
}

/**
 * Unsubscribes the browser from push notifications
 * @return {Promise<void>}
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
export async function disableNotifications()
{
    if (!notificationsSupported()) {
        return;
    }

    let registrations = await navigator.serviceWorker.getRegistrations();

    for (let registration of registrations) {
        let subscription = await registration.pushManager.getSubscription();

        if (subscription) {
            await subscription.unsubscribe();
        }
    }
}

/**
 * Waits until the given service worker registration has an active worker. The global
 * navigator.serviceWorker.ready is not used because the worker has a narrow scope and
 * never controls the page.
 * @param {ServiceWorkerRegistration} registration The registration to wait for
 * @return {Promise<void>}
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
function waitForActivation(registration)
{
    if (registration.active) {
        return Promise.resolve();
    }

    let worker = registration.installing || registration.waiting;

    if (!worker) {
        return Promise.resolve();
    }

    return new Promise(resolve => {
        worker.addEventListener('statechange', () => {
            if (worker.state === 'activated') {
                resolve();
            }
        });
    });
}

/**
 * Converts a base64url VAPID key to the Uint8Array the Push API expects
 * @param {string} base64String The base64url encoded key
 * @return {Uint8Array} The decoded key
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
function urlBase64ToUint8Array(base64String)
{
    let padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    let base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    let rawData = window.atob(base64);
    let outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; i++) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
}
