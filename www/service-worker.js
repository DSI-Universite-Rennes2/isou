// Listen to `push` notification event. Define the text to be displayed
// and show the notification.
self.addEventListener('push', function (event) {
    // Vérifie que l'utilisateur accepte bien les notifications.
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    // Formate les données du message.
    const sendNotification = function(data) {
        return self.registration.showNotification(data.title, {
            body: data.message,
            icon: data.icon,
            url: data.url
            });
    };

    if (event.data) {
        // Envoie le message.
        try {
            var data = event.data.json();
            event.waitUntil(sendNotification(data));
        } catch (exception) {
            console.log('Failed to parse data', exception);
        }
    }
});

// Listen to  `pushsubscriptionchange` event which is fired when
// subscription expires. Subscribe again and register the new subscription
// in the server by sending a POST request with endpoint. Real world
// application would probably use also user identification.
self.addEventListener('pushsubscriptionchange', function(event) {
    event.waitUntil(
        self.registration.pushManager.subscribe({ userVisibleOnly: true })
            .then(function(subscription) {
                const key = subscription.getKey('p256dh');
                const token = subscription.getKey('auth');
                const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

                var data = 'endpoint='+encodeURIComponent(subscription.endpoint)+
                    '&publicKey='+encodeURIComponent(key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null)+
                    '&authToken='+encodeURIComponent(token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null)+
                    '&contentEncoding='+encodeURIComponent(contentEncoding);

                return fetch('/api.php/notifications/enregistrement', {method: 'POST', body: new URLSearchParams(data)});
            })
    );
});
