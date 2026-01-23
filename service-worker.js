const CACHE_NAME = 'orgfiscal-v1';

self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

/**
 * NÃO interceptar requisições de logout ou login
 */
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // Nunca interceptar PHP crítico
  if (
    url.pathname.includes('logout.php') ||
    url.pathname.includes('index.php') ||
    url.pathname.includes('dashboard.php')
  ) {
    return;
  }

  event.respondWith(fetch(event.request));
});
