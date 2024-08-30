const addResourcesToCache = async (resources) => {
    const cache = await chaches.open("v1");
    await cache.addAll(resources);
}

self.addEventListener("install", (event) => {
    console.log("[Service Worker] Install");
    event.waitUntil(
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
        ]),
    );
});

addEventListener("fetch", (event) => {
    event.respondWith(
        (async () => {
            const cachedResponse = await caches.match(event.request);
            if (cachedResponse) return cachedResponse;
            return fetch(event.request);
        })(),
    );
});