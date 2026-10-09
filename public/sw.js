const CACHE_NAME = 'agrico-v2.0.0';

// Install Event - Skip waiting immediately
self.addEventListener('install', (event) => {
  self.skipWaiting();
});

// Activate Event - Clean up all legacy caches completely
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => caches.delete(cache))
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch Event - Never cache HTML page navigations; ALWAYS fetch fresh HTML from PHP server
self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const isHtmlRequest = event.request.mode === 'navigate' || 
                        (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html'));

  // Never cache HTML routes (like /, /scan, /map, /dashboard, etc.)
  if (isHtmlRequest) {
    event.respondWith(fetch(event.request));
    return;
  }

  // Network-first strategy for static assets
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request))
  );
});
