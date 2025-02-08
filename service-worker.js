const CACHE_NAME = 'aquavelo-cache-v2';

self.addEventListener('install', function(event) {
  console.log('Service Worker (v2) installé.');
  event.waitUntil(
    caches.open(CACHE_NAME).then(function(cache) {
      return cache.addAll([
        '/',
        '/centres/Cannes',
        '/centres/Antibes',
        '/conseilminceur',
        '/images/Aquavelo_Icon.png',
        '/images/Aquavelo_Icon_192-M.png',
        '/images/Aquavelo_Icon_M.png'
      ]);
    })
  );
});

self.addEventListener('activate', function(event) {
  console.log('Service Worker (v2) activé.');
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
});

self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request).then(function(response) {
      return response || fetch(event.request);
    })
  );
});

