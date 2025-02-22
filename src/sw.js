/*
// Copyright 2024 留年プロテクタープロジェクト
// This file is part of RPRO.
// 
// RPRO is free software: you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation, either 
// version 3 of the License, or (at your option) any later version.
// 
// RPRO is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
// PURPOSE. See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License along with RPRO.
// If not, see <https://www.gnu.org/licenses/>.
*/

/*
 * sw.js
 * 
 * sw.js is the abbreviation for ServiceWorker.js
 * This file includes function of PWA.
 */
const APPLICCATION_VERSION = "v1.5.1"

/**
 * The file path to be cached passed in the resource
 * argument is added to the cache.
 * @param {*} resources File paths to be cached
 */
const addResourcesToCache = async (resources) => {
    const cache = await caches.open(APPLICCATION_VERSION);
    await cache.addAll(resources);
}

/**
 * Function of saving caches
 */
function installSW() {
    console.log("[process: SW] Caching data...");
    addResourcesToCache([
        "/main.php",
        "/help.php",
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

/**
 * Detects SW installation events and executes cache functions
 */
self.addEventListener("install", (event) => {
    event.waitUntil(installSW());
});

/**
 * It detects requests from the browser, hijacks them, 
 * and returns data stored in the cache. If there is 
 * no match, it retrieves the data from the network 
 * and returns it.
 */
addEventListener("fetch", (event) => {
    event.respondWith((async () => {
        const cachedResponse = await caches.match(event.request);
        if (cachedResponse) {
            console.log("[process: SW] respond from cache");
            return cachedResponse;
        } else if (event.request.url.includes('/asyncSW.php')) {
            console.log("[process: SW] asyncSW");
            return fetch(event.request)
        } else if (event.request.url.includes('/asyncCL.php')) {
            console.log("[process: SW] asyncCL");
            return fetch(event.request)
        } else if (event.request.url.includes('/main-cp.php')) {
            console.log("[process: SW] main-cp");
            return fetch(event.request)
        }
        console.log("[process: SW] respond from network");
        return fetch(event.request, { cache: 'no-cache' });
    })(),
    );



});

/**
 * Delete all caches
 * @returns 
 */
function deleteAllCaches() {
    return caches.keys().then((cacheNames) => {
        return Promise.all(
            cacheNames.map((cacheName) => {
                return caches.delete(cacheName);
            })
        );
    });
}

/**
 * Call this when you want to manually delete a cache.
 * It just wraps deleteAllCaches().
 */
function deleteAllCachesByManual() {
    deleteAllCaches().then(() => {
        console.log("[process: SW] old caches deleted");
        //console.log("[process: SW] new caches installing...");
        //installSW();
    })
}

/**
 * This will be executed automatically to Re-caching when the SW is updated.
 */
self.addEventListener("activate", (event) => {
    event.waitUntil(deleteAllCaches().then(() => {
        console.log("[process: SW] old caches deleted");
        console.log("[process: SW] new caches installing...");
        event.waitUntil(installSW());
    }));
});

/**
 * 
 * @returns cacheVersions(current and latest)
 */
async function getCurrentCacheVersion() {
    const cacheKeys = await caches.keys();
    //console.log("[process: SW] Available Cache Versions:", cacheKeys);
    const currentCacheVersion = cacheKeys.find(key => key.startsWith('v'));
    const newlyCacheVersion = cacheKeys.slice(-1)[0];
    let caV = [currentCacheVersion, newlyCacheVersion];
    return caV;
}
