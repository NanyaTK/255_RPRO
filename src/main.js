/* =========== service Worker 新規インストールイベント ============ */
const registerServiceWorker = async () => {
    if ("serviceWorker" in navigator) {
        try {
            const registration = await navigator.serviceWorker.register("./sw.js", {
                scope: "/src/",
            });
            if (registration.installing) {
                console.log("[process: main] Service worker installing");
            } else if (registration.waiting) {
                console.log("[process: main] Service worker installed");
            } else if (registration.active) {
                console.log("[process: main] Service worker active");
            }
        } catch (error) {
            console.error(`[process: main] Registration failed with ${error}`);
            alert("[process: main -> SW] Registration failed");
        }
    }
}
/* ============================================================== */


/* ==================== インストールボタン関連 ==================== */
registerInstallAppEvent(document.getElementById("install-btn"));
function registerInstallAppEvent(element) {
    registerServiceWorker();
    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        element.promptEvent = event;
        element.style.display = "flex";
        return false;
    });
    function installApp() {
        if (element.promptEvent) {
            element.promptEvent.prompt();
            element.promptEvent.userChoice.then(function (choice) {
                element.style.display = "none";
                element.promptEvent = null;
            });
        } else {
            alert('Error: PWA installation event not detected.\nお使いのデバイスにインストールできません．おま環であると考えられます．\n　　　　　 .┌┐\n　　　　　／ /\n　　　.／　/ i\n　　　|　( ﾟДﾟ) ＜そんなバナナ\n　　　|（ﾉi　　|）\n　　　|　 i　　i\n　　　＼_ヽ＿,ゝ\n　　　　 U" U');
        }
    }
    element.addEventListener("click", installApp);
}
/* ============================================================== */

/* ====================== 再起動ボタン関連 ======================= */
const unregisterSW = document.getElementById("uninstall-btn");
unregisterSW.addEventListener("click", () => {
    navigator.serviceWorker.getRegistrations().then(registrations => {
        if (registrations) {
            for (const registration of registrations) {
                registration.unregister().then((boolean) => {
                    if (boolean === true) {
                        console.log("[process: main] unregister is successful");
                        console.log("[process: main] Service worker uninstalled");
                    }
                    else { console.log("[process: main] unregister is failed"); }
                })
            }
        } else {
            console.log("[process: main] Service worker not found");
        }
    });
    deleteAllCachesByManual();
    //window.location.reload();
});
/* ============================================================== */


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

/* ==================== 時間割自動入力関連 ======================== */
/**
 * 時間割の学科絞り込み関数
 */
function FilterClasses(selectedClassId) {
    const filterSelectElements = document.querySelectorAll(".subject-select");
    filterSelectElements.forEach((filterSelectElement, index) => {
        const AllFilterOptions = filterSelectElement.querySelectorAll("option");
        if (!tempOptions[index]) tempOptions[index] = [];
        AllFilterOptions.forEach(filterOption => {
            tempOptions[index].push(filterOption);
            if (!filterOption.classList.contains("c-" + selectedClassId)) {
                //tempOptions[index].push(filterOption);
                filterOption.remove();
            }
        });
    });
}
/**
 * 時間割の自動入力関数
 */
function AutoCompleteClasses() {
    // 学科・コースのセレクタを取得
    const selectedClass = document.querySelector('.auto-complete');
    const selectedClassOpt = selectedClass.options[selectedClass.selectedIndex];
    const selectedClassId = selectedClassOpt.id;
    // 学期のセレクタを取得
    const selectedTerm = document.querySelector('.term-sel');
    const selectedTermOpt = selectedTerm.options[selectedTerm.selectedIndex];
    const selectedTermId = selectedTermOpt.id;
    console.log("[process: main] " + selectedClassId + "," + selectedTermId);
    FilterClasses(selectedClassId);
    ableRstFlag = true;
    console.log("[process: main] filtering finished.");
}

/**
 * フィルターのリセット関数
 */
function ResetFilter() {
    if (ableRstFlag) {
        const filterSelectElements = document.querySelectorAll(".subject-select");
        filterSelectElements.forEach((filterSelectElement, index) => {
            const AllFilterOptions = filterSelectElement.querySelectorAll("option");
            AllFilterOptions.forEach(filterOption => {
                filterOption.remove();
            });
            if (tempOptions[index]) {
                tempOptions[index].forEach(option => {
                    filterSelectElement.appendChild(option);
                });
            }
            filterSelectElement.options[0].selected = true;
            tempOptions[index] = [];
        });
        ableRstFlag = false;
        console.log("[process: main] filtering reseted.");
    } else {
        ableRstFlag = false;
        console.log("[process: main] filtering was not reseted.");
    }
}

let tempOptions = {};
let ableRstFlag = false;
const cltempBtn = document.getElementById("cltemp-btn");
cltempBtn.addEventListener("click", () => { AutoCompleteClasses(); });
const rstFilterBtn = document.getElementById("rstFilter-btn");
rstFilterBtn.addEventListener("click", () => { ResetFilter(); });

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

    console.log("[process: main] " + registDatas);
    const registJSON = JSON.stringify(registDatas);
    localStorage.setItem('key', registJSON);
    let getval = localStorage.getItem('key');
    let getData = JSON.parse(getval);
    console.log("[process: main] " + getData);

    // JSONデータを文字列にして隠しフィールドにセット
    document.getElementById('jsData').value = JSON.stringify(getData);

    // フラグを設定して、次回ロード時にフォームが自動送信されるようにする
    localStorage.setItem('flag', 1);
    location.reload();
}
/* ============================================================== */

/* ======================= JS-phpデータ渡し ====================== */
// 保存された日時がある場合
const savedTime = localStorage.getItem('savedTime');
const savedTimestamp = parseInt(savedTime, 10); // 文字列を数値に変換

// 現在のタイムスタンプを取得
const currentTime = new Date().getTime();
const currentTimestamp = parseInt(currentTime, 10); // 文字列を数値に変換
localStorage.setItem('savedTime', currentTimestamp); // savedTime を更新

// 時間の差をミリ秒で計算
const timeDifference = currentTimestamp - savedTimestamp;
console.log("[process: main] " + timeDifference);

// 時間差を1秒で切り捨て（1秒未満だと0となる）
const seconds = Math.floor(timeDifference / 1000);

// 現在の時刻が保存された時刻より進んでいて、時間割のデータフラグが立っている場合のみフォーム送信
if (seconds >= 1 && parseInt(localStorage.getItem('flag'))) {
    let getval1 = localStorage.getItem('key');
    let getData2 = JSON.parse(getval1);

    // JSONデータを文字列にして隠しフィールドにセット
    document.getElementById('jsData').value = JSON.stringify(getData2);
    // フォームを自動送信する
    document.getElementById('hiddenForm').submit();
} else {
    console.log('[process: main] The current time is earlier than or equal to the saved time.');
}
/* ============================================================== */

/* ======================= 時間割ポップアップ関連 ====================== */
document.addEventListener('DOMContentLoaded', () => {
    const openButtons = document.querySelectorAll('.open-popup-btn');
    const overlay = document.getElementById('overlay-absent');
    const popup = document.getElementById('popup-absent');
    const closeButton = document.getElementById('close-absent');
    const absentButton = document.querySelectorAll('.absent-btn');

    // ポップアップを表示する関数
    function showPopup() {
        overlay.style.display = 'block';
        //popup.style.display = 'block';
    }

    // ポップアップを閉じる関数
    function hidePopup() {
        overlay.style.display = 'none';
        //popup.style.display = 'none';
    }

    // 各ボタンにクリックイベントを追加
    openButtons.forEach(button => {
        button.addEventListener('click', showPopup);
    });

    // 閉じるボタンにクリックイベントを追加
    closeButton.addEventListener('click', hidePopup);

    // オーバーレイをクリックしたときにもポップアップを閉じる
    overlay.addEventListener('click', hidePopup);
});
/* ============================================================== */