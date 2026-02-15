const CACHE_VERSION = 'saku-ng-v1';
const APP_SHELL_CACHE = CACHE_VERSION + '-app-shell';
const RUNTIME_CACHE = CACHE_VERSION + '-runtime';

const APP_SHELL_FILES = ['/', '/manifest.webmanifest', '/favicon.ico', '/apple-touch-icon.png'];

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(APP_SHELL_CACHE).then(function (cache) {
            return cache.addAll(APP_SHELL_FILES);
        }).then(function () {
            return self.skipWaiting();
        })
    );
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys
                    .filter(function (key) {
                        return key !== APP_SHELL_CACHE && key !== RUNTIME_CACHE;
                    })
                    .map(function (key) {
                        return caches.delete(key);
                    })
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});

self.addEventListener('fetch', function (event) {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET') {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then(function (response) {
                    const responseClone = response.clone();
                    caches.open(RUNTIME_CACHE).then(function (cache) {
                        cache.put(request, responseClone);
                    });
                    return response;
                })
                .catch(function () {
                    return caches.match(request).then(function (cached) {
                        return cached || caches.match('/');
                    });
                })
        );
        return;
    }

    if (url.origin === self.location.origin) {
        event.respondWith(
            caches.match(request).then(function (cached) {
                const networkFetch = fetch(request)
                    .then(function (response) {
                        const responseClone = response.clone();
                        caches.open(RUNTIME_CACHE).then(function (cache) {
                            cache.put(request, responseClone);
                        });
                        return response;
                    })
                    .catch(function () {
                        return cached;
                    });

                return cached || networkFetch;
            })
        );
    }
});
