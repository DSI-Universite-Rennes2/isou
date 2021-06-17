const isou_url = window.location.href.split('/index.php')[0].replace(/\/+$/, '');

function setIsouActiveNotificationImage(active = true) {
    var button = document.getElementById('isou-top-aside-notifications-button');
    var image = document.getElementById('isou-top-aside-notifications-image');
    var submit = document.getElementById('modal-notifications-submit');

    if (button === false || image === false || submit === false) {
        return;
    }

    if (active === true) {
        button.classList.add('alert-success');
        button.classList.remove('alert-danger');

        image.setAttribute('alt', 'notification activée');
        image.setAttribute('src', isou_url+'/themes/bootstrap3/images/notifications-on.svg');

        submit.className = 'btn btn-danger';
        submit.textContent = 'Désactiver les notifications';
    } else {
        button.classList.add('alert-danger');
        button.classList.remove('alert-success');

        image.setAttribute('alt', 'notification désactivée');
        image.setAttribute('src', isou_url+'/themes/bootstrap3/images/notifications-off.svg');

        submit.className = 'btn btn-success';
        submit.textContent = 'Activer les notifications';
    }
}

function subscribeIsouNotifications() {
    navigator.serviceWorker.ready
        .then(function(serviceWorkerRegistration) {
            fetch(isou_url+'/api.php/notifications/cle-publique-serveur')
                .then(function(response) {
                    return response.json(); // Transform the data into json
                })
                .then(function(response) {
                    if (response.status !== 200) {
                        return;
                    }

                    // Convertis la clé publique du serveur au bon format.
                    var publicKeyServer = response.message;

                    const padding = '='.repeat((4 - publicKeyServer.length % 4) % 4);

                    const base64 = (publicKeyServer + padding)
                        .replace(/\-/g, '+')
                        .replace(/_/g, '/');

                    const rawData = window.atob(base64);
                    const publicKeyServerArray = new Uint8Array(rawData.length);
                    for (let i = 0; i < rawData.length; ++i) {
                        publicKeyServerArray[i] = rawData.charCodeAt(i);
                    }

                    var options = {
                        userVisibleOnly: true,
                        applicationServerKey: publicKeyServerArray
                    };

                    serviceWorkerRegistration.pushManager.subscribe(options)
                        .then(function(subscription) {
                            const key = subscription.getKey('p256dh');
                            const token = subscription.getKey('auth');
                            const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

                            var data = 'endpoint='+encodeURIComponent(subscription.endpoint)+
                                '&publicKey='+encodeURIComponent(key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null)+
                                '&authToken='+encodeURIComponent(token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null)+
                                '&contentEncoding='+encodeURIComponent(contentEncoding);

                            fetch(isou_url+'/api.php/notifications/enregistrement', {method: 'POST', body: new URLSearchParams(data)})
                                .then(function(response) {
                                    setIsouActiveNotificationImage(true);
                                },
                                function(error) {
                                    // Error.
                                });
                           });
                }, function(error) {
                    // TODO.
                });
        });
}

function unsubscribeIsouNotifications() {
    navigator.serviceWorker.ready
        .then(function(serviceWorkerRegistration) {
            serviceWorkerRegistration.pushManager.getSubscription()
                .then(function(subscription) {
                    subscription.unsubscribe().then(function(successful) {
                        // You've successfully unsubscribed
                        const key = subscription.getKey('p256dh');
                        const token = subscription.getKey('auth');
                        const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

                        var data = 'endpoint='+encodeURIComponent(subscription.endpoint)+
                            '&publicKey='+encodeURIComponent(key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null)+
                            '&authToken='+encodeURIComponent(token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null)+
                            '&contentEncoding='+encodeURIComponent(contentEncoding);

                        fetch(isou_url+'/api.php/notifications/desinscription', {method: 'DELETE', body: new URLSearchParams(data)})
                            .then(function(response) {
                                setIsouActiveNotificationImage(false);
                            },
                            function(error) {
                                // Error.
                            });
                       });
                    }).catch(function(e) {
                        // Unsubscription failed
                    })
        });
}

// Lorsque la page est chargée...
document.addEventListener('DOMContentLoaded', function() {
    var notificationsButton = document.getElementById('isou-top-aside-notifications-button');

    if (notificationsButton === false) {
        // Les notifications ne sont pas activées sur Isou.
        return;
    }

    if (!('serviceWorker' in navigator)) {
        // Supprime la cloche lorsque les serviceworker ne sont pas activés sur le navigateur.
        notificationsButton.remove();

        console.log('Les ServiceWorker ne sont pas gérés ou activés par votre navigateur.');
        return;
    }

    if (!('PushManager' in window)) {
        // Supprime la cloche lorsque les push ne sont pas activés sur le navigateur.
        notificationsButton.remove();

        console.log('Les Push ne sont pas gérés ou activés par votre navigateur.');
        return;
    }

    try {
        // Essaye d'enregistrer le service-worker.
        navigator.serviceWorker.register(isou_url+'/service-worker.js')
            .then(function() {
                    // Service worker has been registered.
                },
                function(error) {
                    console.log('Service worker registration failed', error);
                }
            );

        navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
            serviceWorkerRegistration.pushManager.getSubscription()
                .then(function(subscription) {
                    // Initialise l'image de notification.
                    var image = document.createElement('img');
                    image.setAttribute('id', 'isou-top-aside-notifications-image');
                    image.setAttribute('height', '16px');
                    image.setAttribute('width', '16px');

                    notificationsButton.append(image);

                    if (subscription) {
                        setIsouActiveNotificationImage(true);
                    } else {
                        setIsouActiveNotificationImage(false);
                    }

                    // Gère les inscriptions/désinscriptions aux notifications.
                    notificationsButton.addEventListener('click', function() {
                        var modal = document.getElementById('modal-notifications');
                        modal.style.display = 'unset';
                        modal.classList.remove('hidden');

                        document.getElementById('modal-backdrop').classList.remove('hidden');
                    });

                    // Gère le bouton de fermeture de la modal de notification.
                    var close = document.getElementById('modal-notifications-close');
                    close.addEventListener('click', function() {
                        var modal = document.getElementById('modal-notifications');
                        modal.style.display = 'none';
                        modal.classList.add('hidden');

                        document.getElementById('modal-backdrop').classList.add('hidden');
                    });

                    // Gère le bouton d'activation/désactivation de la modal de notification.
                    var submit = document.getElementById('modal-notifications-submit');
                    submit.addEventListener('click', function() {
                        navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
                            serviceWorkerRegistration.pushManager.getSubscription()
                                .then(function(subscription) {
                                    if (subscription) {
                                        unsubscribeIsouNotifications();
                                    } else {
                                        subscribeIsouNotifications();
                                    }
                                })
                        })
                    });
                });
            });
    } catch (exception) {
        console.log('Service-worker exception: ', exception);
    }
});
