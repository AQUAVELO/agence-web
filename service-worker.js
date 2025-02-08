self.addEventListener('install', function(event) {
  console.log('Service Worker installé.');

  event.waitUntil(
    caches.open('aquavelo-cache').then(function(cache) {
      return cache.addAll([
        '/',
        '/centres/Cannes',
        '/centres/Antibes',
        '/conseilminceur',                    // ✅ Ajout de Conseil Minceur
        '/images/Aquavelo_Icon.png',
        '/images/Aquavelo_Icon_192-M.png',    // ✅ Nouvelle icône
        '/images/Aquavelo_Icon_M.png'         // ✅ Nouvelle icône
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
