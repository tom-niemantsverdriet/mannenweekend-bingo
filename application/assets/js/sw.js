/**
 * Service worker for the Mannenweekend Bingo push notifications.
 *
 * Receives payloadless push messages and shows a notification. Clicking it focuses
 * (or opens) the bingo card.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */

self.addEventListener('push', function (event) {
    let title = 'Mannenweekend Bingo';
    let options = {
        body: 'Er is een nieuw vakje afgevinkt!',
        icon: '/application/assets/images/icon.png',
        badge: '/application/assets/images/icon.png',
    };

    // Payloadless pushes have no data; fall back to the default message when absent

    if (event.data) {
        try {
            let payload = event.data.json();
            title = payload.title || title;
            options.body = payload.body || options.body;
        } catch (error) {
            options.body = event.data.text() || options.body;
        }
    }

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    event.waitUntil(
        self.clients.matchAll({type: 'window', includeUncontrolled: true}).then(function (clientList) {
            for (let client of clientList) {
                if ('focus' in client) {
                    return client.focus();
                }
            }

            if (self.clients.openWindow) {
                return self.clients.openWindow('/');
            }
        })
    );
});
