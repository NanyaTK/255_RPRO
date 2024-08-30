/* =========== service Worker 新規インストールイベント ============ */
const registerServiceWorker = async () => {
    if ("serviceWorker" in navigator) {
        try {
            const registration = await navigator.serviceWorker.register("./sw.js", {
                scope: "/",
            });
            if (registration.installing) {
                console.log("Service worker installing");
            } else if (registration.waiting) {
                console.log("Service worker installed");
            } else if (registration.active) {
                console.log("Service worker active");
            }
        } catch (error) {
            console.error(`Registration failed with ${error}`);
        }
    }
}
/* ============================================================== */

//バナーの代わりに表示するボタンを登録する
registerInstallAppEvent(document.getElementById("install-btn"));

//バナー表示をキャンセルし、代わりに表示するDOM要素を登録する関数
//引数１：イベントを登録するHTMLElement
function registerInstallAppEvent(element) {
    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault(); //バナー表示をキャンセル
        element.promptEvent = event; //eventを保持しておく
        element.style.display = "flex";
        return false;
    });
    //インストールダイアログの表示処理
    function installApp() {
        if (element.promptEvent) {
            element.promptEvent.prompt(); //ダイアログ表示
            element.promptEvent.userChoice.then(function (choice) {
                element.style.display = "none"
                element.promptEvent = null; //一度しか使えないため後始末
            });
        } else {
            alert('Error: PWA installation event not detected.\nお使いのデバイスにインストールできません．おま環であると考えられます．\n　　　　　 .┌┐\n　　　　　／ /\n　　　.／　/ i\n　　　|　( ﾟДﾟ) ＜そんなバナナ\n　　　|（ﾉi　　|）\n　　　|　 i　　i\n　　　＼_ヽ＿,ゝ\n　　　　 U" U');
        }
    }
    element.addEventListener("click", installApp);
}

/* ==================== 新規登録ボタンイベント ==================== */
const signUpBtn = document.getElementById('signup-btn');
const popupWrapper = document.getElementById('popup-wrapper');
const close = document.getElementById('close');

// ボタンをクリックしたときにポップアップを表示させる
signUpBtn.addEventListener('click', () => {
    popupWrapper.style.display = "block";
});

// ポップアップの外側又は「x」のマークをクリックしたときポップアップを閉じる
popupWrapper.addEventListener('click', e => {
    if (e.target.id === popupWrapper.id || e.target.id === close.id) {
        popupWrapper.style.display = 'none';
    }
});
/* ============================================================== */


/* ================== 新規登録確定ボタンイベント ================== */
function getAllSelectedOptionIds() {
    // .subject-selectクラスを持つ全てのselect要素を取得
    const selectElements = document.querySelectorAll('.subject-select');
    const selectedOptionIds = [];
    // 各select要素をループして選択されたoptionのidを取得
    selectElements.forEach(selectElement => {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const selectedOptionId = selectedOption.id;
        selectedOptionIds.push(selectedOptionId); // 配列に追加
    });

    // 結果を表示
    document.getElementById("result").innerText = "Selected Option IDs: " + selectedOptionIds.join(', ');
    console.log(selectedOptionIds);
    const registDatas = [];

    for (let i = 0; i < selectedOptionIds.length; i++) {
        const registData = selectedOptionIds[i];
        registDatas.push(registData);
    }

    console.log(registDatas);
    const registJSON = JSON.stringify(registDatas);
    localStorage.setItem('key', registJSON);
    let getval = localStorage.getItem('key');
    let getData = JSON.parse(getval);
    console.log(getData);
}
/* ============================================================== */