const CACHE_NAME = 'aquavelo-cache-v3';

self.addEventListener('install', function(event) {
  console.log('Service Worker (v3) installé.');

  event.waitUntil(
    caches.open(CACHE_NAME).then(function(cache) {
      return cache.addAll([
        '/',
        '/centres/Cannes',
        '/centres/Antibes',
        '/conseilminceur',
        '/images/Aquavelo_Icon_C.png',
        '/images/Aquavelo_Icon_512_C.png',
        '/images/Aquavelo_Icon_A.png',
        '/images/Aquavelo_Icon_512_A.png',
        '/images/Aquavelo_Icon_M.png',
        '/images/Aquavelo_Icon_512_M.png'
      ]);
    })
  );
});

self.addEventListener('activate', function(event) {
  console.log('Service Worker (v3) activé.');
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


