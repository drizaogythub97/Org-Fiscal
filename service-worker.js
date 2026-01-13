const CACHE_NAME = 'orgfiscal-v1';

const FILES_TO_CACHE = [
  '/',
  '/index.php',
  '/assets/css/reset.css',
  '/assets/css/variables.css',
  '/assets/css/main.css',
  '/assets/img/logo-orgfiscal.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(FILES_TO_CACHE);
    })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request);
    })
  );
});
