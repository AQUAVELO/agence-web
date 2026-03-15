const CACHE_NAME = 'aquavelo-cache-v5';

// Ressources statiques uniquement (pas les pages HTML dynamiques)
const STATIC_ASSETS = [
  '/images/Aquavelo_Icon_C.png',
  '/images/Aquavelo_Icon_192_C.png',
  '/images/Aquavelo_Icon_A.png',
  '/images/Aquavelo_Icon_192_A.png',
  '/images/Aquavelo_Icon_M.png',
  '/images/Aquavelo_Icon_192_M.png'
];

self.addEventListener('install', function(event) {
  console.log('Service Worker (v5) installé.');
  event.waitUntil(
    caches.open(CACHE_NAME).then(function(cache) {
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', function(event) {
  console.log('Service Worker (v5) activé.');
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.map(function(cache) {
          if (cache !== CACHE_NAME) {
            console.log('Suppression du cache obsolète:', cache);
            return caches.delete(cache);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', function(event) {
  const url = new URL(event.request.url);

  // Ne jamais mettre en cache les pages PHP/HTML dynamiques
  if (event.request.mode === 'navigate' || 
      url.pathname.endsWith('.php') ||
      url.pathname === '/' ||
      url.pathname.startsWith('/centres/') ||
      url.pathname.startsWith('/aquabiking') ||
      url.pathname.startsWith('/aquagym') ||
      url.pathname.startsWith('/free') ||
      url.pathname.startsWith('/conseilminceur')) {
    event.respondWith(fetch(event.request));
    return;
  }

  // Pour les assets statiques : cache en priorité
  event.respondWith(
    caches.match(event.request).then(function(response) {
      return response || fetch(event.request);
    })
  );
});


