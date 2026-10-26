// EFPT_AMS Service Worker
const CACHE_NAME = 'efpt-ams-v3';
const STATIC_ASSETS = [
    './assets/css/style.css',
    './assets/js/main.js',
    './assets/js/chart.umd.min.js',
    './assets/images/efplogo.png',
    './manifest.json'
];

// Install event - cache static assets
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            return Promise.allSettled(
                STATIC_ASSETS.map(function(url) {
                    return cache.add(url).catch(function(err) {
                        console.log('SW install: failed to cache ' + url, err);
                    });
                })
            );
        }).then(function() {
            return self.skipWaiting();
        })
    );
});

// Activate event - clean old caches
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.filter(function(name) {
                    return name !== CACHE_NAME;
                }).map(function(name) {
                    return caches.delete(name);
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event - network first, fallback to cache for static assets
self.addEventListener('fetch', function(event) {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    var requestUrl = new URL(event.request.url);
    var isVersionedAsset = requestUrl.pathname.indexOf('/assets/') !== -1
        && requestUrl.searchParams.has('v');
    if (isVersionedAsset) {
        event.respondWith(
            caches.match(event.request).then(function(cachedResponse) {
                if (cachedResponse) return cachedResponse;
                return fetch(event.request).then(function(response) {
                    if (!response || response.status !== 200) return response;
                    var responseClone = response.clone();
                    return caches.open(CACHE_NAME).then(function(cache) {
                        return cache.put(event.request, responseClone).then(function() {
                            return response;
                        });
                    }).catch(function() {
                        return response;
                    });
                }).catch(function() {
                    return new Response('Network error. Please check your connection.', {
                        status: 503,
                        statusText: 'Service Unavailable',
                        headers: new Headers({ 'Content-Type': 'text/plain' })
                    });
                });
            })
        );
        return;
    }

    // For PHP pages and dynamic content, always use network only
    if (requestUrl.pathname.includes('.php') || requestUrl.search) {
        event.respondWith(
            fetch(event.request).catch(function() {
                // Network failed, return a simple error response instead of rejecting
                return new Response('Network error. Please check your connection.', {
                    status: 503,
                    statusText: 'Service Unavailable',
                    headers: new Headers({ 'Content-Type': 'text/plain' })
                });
            })
        );
        return;
    }

    // For static assets, try network first, then cache
    event.respondWith(
        fetch(event.request).then(function(response) {
            // Cache successful responses
            if (response && response.status === 200) {
                var responseClone = response.clone();
                caches.open(CACHE_NAME).then(function(cache) {
                    cache.put(event.request, responseClone);
                });
            }
            return response;
        }).catch(function() {
            // Network failed, try cache
            return caches.match(event.request).then(function(cachedResponse) {
                if (cachedResponse) {
                    return cachedResponse;
                }
                // No cache either, return error
                return new Response('Offline and no cache available.', {
                    status: 503,
                    statusText: 'Service Unavailable',
                    headers: new Headers({ 'Content-Type': 'text/plain' })
                });
            });
        })
    );
});
