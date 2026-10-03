/**
 * ============================================================
 * Service Worker v2.1 — Yass Digital Lab PWA
 * ============================================================
 * Gestion avancée du cache hors-ligne et fallback SPA Vue Router.
 * ============================================================
 */

const CACHE_NAME = 'yass-lab-pwa-v2';

const ASSETS_TO_CACHE = [
  '/',
  '/index.html',
  '/manifest.json',
  '/favicon.svg',
];

// Installation : Mise en cache initiale des fichiers vitaux de l'application
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(ASSETS_TO_CACHE);
    }).then(() => self.skipWaiting())
  );
});

// Activation : Nettoyage des anciennes versions de cache
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames
          .filter(name => name !== CACHE_NAME)
          .map(name => caches.delete(name))
      );
    }).then(() => self.clients.claim())
  );
});

// Interception des requêtes HTTP
self.addEventListener('fetch', event => {
  const request = event.request;

  // Contourner le Service Worker pour les requêtes non-HTTP/GET, médias streaming et appels API
  if (
    request.method !== 'GET' ||
    !request.url.startsWith('http') ||
    request.headers.has('range') ||
    request.url.match(/\.(mp4|webm|ogg|mp3|wav)$/i) ||
    request.url.includes('/api/')
  ) {
    return;
  }

  event.respondWith(
    caches.match(request).then(cachedResponse => {
      if (cachedResponse) {
        // En arrière-plan, tenter de rafraîchir la ressource réseau si possible
        fetch(request).then(networkResponse => {
          if (networkResponse && networkResponse.status === 200) {
            caches.open(CACHE_NAME).then(cache => cache.put(request, networkResponse));
          }
        }).catch(() => {});

        return cachedResponse;
      }

      // Si pas en cache, récupérer sur le réseau
      return fetch(request).then(networkResponse => {
        return networkResponse;
      }).catch(async () => {
        // Fallback Hors-Ligne garanti pour les navigations SPA Vue Router
        if (request.headers.get('accept') && request.headers.get('accept').includes('text/html')) {
          const fallback = await caches.match('/index.html');
          if (fallback) return fallback;
        }

        // Toujours retourner une instance de Response valide pour éviter TypeError dans SW
        return new Response('Ressource indisponible hors-ligne', {
          status: 503,
          statusText: 'Service Unavailable',
          headers: new Headers({ 'Content-Type': 'text/plain; charset=utf-8' })
        });
      });
    })
  );
});
