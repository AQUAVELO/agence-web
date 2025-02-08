self.addEventListener('install', function(event) {
  console.log('Service Worker installé.');
  event.waitUntil(
    caches.open('aquavelo-cache').then(function(cache) {
      return cache.addAll([
        '/',
        '/centres/Cannes',
        '/centres/Antibes',
        '/images/Aquavelo_Icon.png'
      ]);
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
