self.addEventListener('push', (event) => {
    if (!event.data) {
        return;
    }

    const data = event.data.json();
    event.waitUntil(self.registration.showNotification(data.title || 'Shumburo', {
        body: data.body || '',
        icon: data.icon || '/favicon.ico',
        badge: data.icon || '/favicon.ico',
        data: {url: data.url || '/notifications'},
    }));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});