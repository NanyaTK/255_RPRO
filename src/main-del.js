document.addEventListener("DOMContentLoaded", async () => {
    let flag = 0
    if ('serviceWorker' in navigator) {
        const registration = await navigator.serviceWorker.getRegistration();
        if (!registration) {
            flag = 1
        }
    } else {
    }
    if (flag && !(localStorage.length > 0)) {
        const devElement = document.getElementById("hide")
        if (devElement) {
            devElement.style.display = "none";
        }
    }
});


async function deleteAllCaches() {
    return caches.keys().then((cacheNames) => {
        return Promise.all(
            cacheNames.map((cacheName) => {
                return caches.delete(cacheName);
            })
        );
    });
}
async function uninstallServiceWorker() {
    if ('serviceWorker' in navigator) {
        const registration = await navigator.serviceWorker.getRegistration();
        if (registration) {
            await registration.unregister()
            console.log("Successed to Uninstall Service Worker.")
        } else {
            console.log("No Service Worker detected.")
        }
    } else {
        alert("SW非対応機器です．処理を続行します．")
    }
}
async function delCacheAndSW() {
    await deleteAllCaches()
    await uninstallServiceWorker()
    alert("キャッシュを正常に削除しました．\nご利用ありがとうございました．")
    window.location.reload()
}

const delBtn = document.getElementById("delete-btn")
delBtn.addEventListener('click', () => {
    const delLocalstrage = new Promise((resolve, reject) => {
        localStorage.clear();
        if (localStorage.length > 0) {
            reject("error. key detected." + localStorage.key(0))
        } else {
            resolve("key deleted.")
        }
    })
    delLocalstrage.then((message) => {
        alert(message + "\nローカルストレージのデータを正常に削除しました．\nキャッシュ削除を開始します．\n101 : " + message)
        delCacheAndSW()
    }).catch((err) => {
        alert("ローカルストレージの削除に失敗しました．\n処理を中断します．\n102 : " + err)
    })
})

