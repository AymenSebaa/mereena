const url = 'https://ai-trolley.com/mereena/';

const app = "mereena";
const assets = [
    `${url}/`,
    `${url}/index.php`,
];

self.addEventListener("install", installEvent => {
    installEvent.waitUntil(
        caches.open(app).then(cache => {
            cache.addAll(assets)
        })
    )
});

self.addEventListener("fetch", fetchEvent => {
    fetchEvent.respondWith(
        caches.match(fetchEvent.request).then(res => {
            return res || fetch(fetchEvent.request)
        })
    )
});

self.addEventListener("push", (e) => {
    console.log('app.js', e.data);
    const {title, body, url} = e.data.json();

    self.registration.showNotification(title, {
        body: body,
        icon: 'icons/logo-round.png',
        data: { url }
    });
});

self.addEventListener('notificationclick', (e) => {
    clients.openWindow(e.notification.data.url)
    e.notification.close();
});