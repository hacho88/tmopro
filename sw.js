const CACHE_NAME = 'tmopro-pwa-v13';
const APP_SHELL = [
  './',
  './index.php',
  './checkout.php',
  './style.css',
  './vue.global.prod.js',
  './settings.json',
  './products.json',
  './manifest.json',
  './icon.svg'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(APP_SHELL)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => Promise.all(keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key)))).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') {
    return;
  }

  const url = new URL(event.request.url);
  if (url.pathname.endsWith('/style.css') || url.pathname.endsWith('/index.php')) {
    event.respondWith(fetch(event.request, { cache: 'reload' }));
    return;
  }
  if (url.pathname.endsWith('/admin.php')) {
    event.respondWith(fetch(event.request));
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then(response => {
        const copy = response.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
        return response;
      })
      .catch(() => caches.match(event.request).then(cached => cached || caches.match('./index.php')))
  );
});
