const addResourcesToCache = async (resources) => {
    const cache = await caches.open("v3");
    await cache.addAll(resources);
}

function installSW() {
    console.log("[process: SW] Caching data...");
    addResourcesToCache([
        "/",
        "/main.php",
        "/main.js",
        "/sw.js",
        "/main.css",
        "/mainManifest.json",
        "/screenshots/ss1.webp",
        "/icon-images/48.png",
        "/icon-images/72.png",
        "/icon-images/96.png",
        "/icon-images/128.png",
        "/icon-images/192.png",
        "/icon-images/384.png",
        "/icon-images/512.png",
        "/icon-images/old_192.png",
        "/icon-images/old_512.png",
        "/icon-images/favicon.ico",
    ]);
    console.log("[process: SW] Cache complete");
}

self.addEventListener("install", (event) => {
    event.waitUntil(installSW());
});

addEventListener("fetch", (event) => {
    event.respondWith(
        (async () => {
            const cachedResponse = await caches.match(event.request);
            if (cachedResponse) {
                console.log("[process: SW] respond from cache");
                return cachedResponse;
            };
            console.log("[process: SW] respond from network");
            return fetch(event.request);
        })(),
    );
});

function deleteAllCaches() {
    return caches.keys().then((cacheNames) => {
        return Promise.all(
            cacheNames.map((cacheName) => {
                return caches.delete(cacheName);
            })
        );
    });
}

function deleteAllCachesByManual() {
    deleteAllCaches().then(() => {
        console.log("[process: SW] old caches deleted");
        console.log("[process: SW] new caches installing...");
    })
}

self.addEventListener("activate", (event) => {
    event.waitUntil(deleteAllCachesByManual());
    //event.waitUntil(installSW());
});
