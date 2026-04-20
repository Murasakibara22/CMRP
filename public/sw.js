const CACHE_NAME  = 'cmrp-fidele-v1';
const OFFLINE_URL = '/offline';

/* Ressources mises en cache à l'installation */
const PRECACHE = [
    '/customer/home',
    '/offline',
    '/manifest.json',
    '/images/icons/android/android-launchericon-192-192.png',
    '/images/icons/android/android-launchericon-512-512.png',
];

/* ── Install ── */
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(PRECACHE))
            .then(() => self.skipWaiting())
    );
});

/* ── Activate : nettoyer les anciens caches ── */
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

/* ── Fetch : Network first, cache fallback ── */
self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;
    if (event.request.url.includes('/livewire/'))  return;
    if (event.request.url.includes('/api/'))       return;
    if (event.request.url.includes('__clockwork')) return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                }
                return response;
            })
            .catch(() =>
                caches.match(event.request)
                    .then(cached => cached || caches.match(OFFLINE_URL))
            )
    );
});

/* ── Push notifications ── */
self.addEventListener('push', event => {
    const data = event.data?.json() ?? {};
    event.waitUntil(
        self.registration.showNotification(data.title ?? 'CMRP', {
            body:    data.body  ?? 'Vous avez une notification.',
            icon:    '/images/icons/android/android-launchericon-192-192.png',
            badge:   '/images/icons/android/android-launchericon-72-72.png',
            data:    { url: data.url ?? '/customer/home' },
            vibrate: [200, 100, 200],
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});
