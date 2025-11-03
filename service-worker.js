// Copyright FXBG Closet 2025

const CACHE_NAME = "fxbg-closet-v1";
const STATIC_ASSETS = [
  "/",
  "/index.php",
  "/login.php",
  "/css/base.css",
  "/css/theme-toggle.css",
  "/css/normal_base.css",
  "/css/management_base.css",
  "/js/theme-toggle.js",
  "/images/FXBG-PrideWhiteLogo.png",
  "https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap",
  "https://kit.fontawesome.com/yourkit.js",
];

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches
      .open(CACHE_NAME)
      .then((cache) => {
        console.log("Service Worker: Caching static assets");
        return Promise.allSettled(
          STATIC_ASSETS.map((url) =>
            fetch(url)
              .then((response) => {
                if (response.status === 200) {
                  return cache.add(url);
                }
              })
              .catch(() => {}),
          ),
        );
      })
      .catch((error) => {
        console.log("Service Worker: Cache install failed:", error);
      }),
  );
  self.skipWaiting();
});

self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log("Service Worker: Deleting old cache:", cacheName);
            return caches.delete(cacheName);
          }
        }),
      );
    }),
  );
  self.clients.claim();
});

self.addEventListener("fetch", (event) => {
  const { request } = event;

  if (!request.url.startsWith(self.location.origin)) {
    return;
  }

  if (request.url.includes(".php")) {
    event.respondWith(
      fetch(request)
        .then((response) => {
          if (
            !response ||
            response.status !== 200 ||
            response.type !== "basic"
          ) {
            return response;
          }
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(request, responseClone);
          });
          return response;
        })
        .catch(() => {
          return caches.match(request).then((cachedResponse) => {
            return (
              cachedResponse ||
              new Response("Offline - Page not cached", { status: 503 })
            );
          });
        }),
    );
  } else {
    event.respondWith(
      caches.match(request).then((cachedResponse) => {
        if (cachedResponse) {
          return cachedResponse;
        }
        return fetch(request)
          .then((response) => {
            if (!response || response.status !== 200) {
              return response;
            }
            const responseClone = response.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(request, responseClone);
            });
            return response;
          })
          .catch(() => {
            return new Response("Offline - Asset not available", {
              status: 503,
            });
          });
      }),
    );
  }
});
