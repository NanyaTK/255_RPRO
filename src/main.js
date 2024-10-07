/*
 * Copyright 2024 留年プロテクタープロジェクト
 * This file is part of RPRO.
 * 
 * RPRO is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either 
 * version 3 of the License, or (at your option) any later version.
 * 
 * RPRO is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
 * PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with RPRO.
 * If not, see <https://www.gnu.org/licenses/>.
 */

/*
 * main.js
 * 
 * main.js is the main file of RPRO app functions.
 */
const hostname = window.location.hostname;
const queryParams = new URLSearchParams(window.location.search);
const environment = queryParams.get('env');
if (hostname === 'rpro.nanyatk.com' && environment === 'dev') {
    const DEVFLAG = true;
} else if (hostname === 'rpro.nanyatk.com') {
    const DEVFLAG = false;
} else {
    const DEVFLAG = true;
}

/* =========== service Worker 新規インストールイベント ============ */
const registerServiceWorker = async () => {
    if ("serviceWorker" in navigator) {
        try {
            const registration = await navigator.serviceWorker.register("./sw.js", {
                scope: "/",
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
            element.promptEvent.userChoice.then(function () {
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
        tempOptions[index] = [];
        AllFilterOptions.forEach(filterOption => {
            tempOptions[index].push(filterOption);
            if ((!filterOption.classList.contains("c-" + selectedClassId)) && (!filterOption.classList.contains("empty"))) {
                //tempOptions[index].push(filterOption);
                filterOption.remove();
            }
        });
    });
}
/**
 * 時間割のフィルタリング関数
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
    const CTData = "0," + selectedClassId + "," + selectedTermId;
    console.log("[process: main] " + CTData);
    FilterClasses(selectedClassId);
    ableRstFlag = true;
    console.log("[process: main] filtering finished.");
    return CTData;
}

/**
 * フィルターのリセット関数
 */
function ResetFilter() {
    if (ableRstFlag) {
        const filterSelectElements = document.querySelectorAll(".subject-select");
        filterSelectElements.forEach((filterSelectElement, index) => {
            while (filterSelectElement.firstChild) {
                filterSelectElement.removeChild(filterSelectElement.firstChild);
            }
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
let temp = {};
let ableRstFlag = false;
const cltempBtn = document.getElementById("cltemp-btn");
cltempBtn.addEventListener("click", () => {
    const CTData = AutoCompleteClasses();
    fetch('asyncSW.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(CTData) // 必要なデータを送信
    })
        .then(response => response.json())
        .then(data => {
            console.log('[process: asyncSW] ', data);
            if (data) {
                let clID = data.split(',');
                console.log('[process: asyncSW] ', clID);
                const classElements = document.querySelectorAll(".subject-select");
                classElements.forEach((classElement) => {
                    //console.log(clID[0]);
                    if (clID[0] != 0) {
                        //console.log("cs-" + (clID[0]));
                        classElement.options["cs-" + (clID[0])].selected = true;
                    } else {
                        classElement.options[0].selected = true;
                    }
                    clID.splice(0, 1);
                })
            }
        })
        .catch(error => {
            console.error('[process: asyncSW] ', error);
        });
});
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

    registDatas.unshift("1");

    console.log("[process: main] " + registDatas);
    const registJSON = JSON.stringify(registDatas);
    localStorage.setItem('key', registJSON);
    let getval = localStorage.getItem('key');
    let getData = JSON.parse(getval);
    console.log("[process: main] " + getData);

    // 下 新規作成部分記入開始
    fetch('asyncSW.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(getData) // 必要なデータを送信
    })
        .then(response => response.json())
        .then(data => {
            console.log('[process: asyncSW] ', data);
            if (data) {
                let clID = data.split(',');
                console.log('[process: asyncSW] ', clID);
                const classElements = document.querySelectorAll(".subject-select");
                classElements.forEach((classElement) => {
                    //console.log(clID[0]);
                    if (clID[0] != 0) {
                        //console.log("cs-" + (clID[0]));
                        classElement.options["cs-" + (clID[0])].selected = true;
                    } else {
                        classElement.options[0].selected = true;
                    }
                    clID.splice(0, 1);
                })
            }
        })
        .catch(error => {
            console.error('[process: asyncSW] ', error);
        });

    /*
    // JSONデータを文字列にして隠しフィールドにセット
    document.getElementById('jsData').value = JSON.stringify(getData);

    // フラグを設定して、次回ロード時にフォームが自動送信されるようにする
    localStorage.setItem('flag', 1);
    location.reload();
    */
}
/* ============================================================== */

/* ======================= JS-phpデータ渡し ====================== */
// 保存された日時がある場合
/*
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
*/
/* ============================================================== */

/* ======================= 時間割ポップアップ関連 ====================== */
document.addEventListener('DOMContentLoaded', () => {
    const openButtons = document.querySelectorAll('[class ^="open-popup-btn"]');
    const overlays = document.querySelectorAll('[class ^="overlay-absent"]');
    const popup = document.getElementById('popup-absent');
    const closeButtons = document.querySelectorAll('close-absent');
    const absentButton = document.querySelectorAll('.absent-btn');

    // ポップアップを表示する関数
    function showPopup() {
        overlays[this.num].style.display = 'block';
        //popup.style.display = 'block';
    }

    // ポップアップを閉じる関数
    function hidePopup() {
        overlays.forEach(overlay => {
            overlay.style.display = 'none';
        });
        //popup.style.display = 'none';
    }

    // 各ボタンにクリックイベントを追加
    let i = 0;
    openButtons.forEach(button => {
        button.addEventListener('click', { num: i++, handleEvent: showPopup });
    });

    // 閉じるボタンにクリックイベントを追加
    closeButtons.forEach(closeButton => {
        closeButton.addEventListener('click', hidePopup);
    });

    // オーバーレイをクリックしたときにもポップアップを閉じる
    overlays.forEach(overlay => {
        overlay.addEventListener('click', hidePopup);
    });
});
/* ============================================================== */

/* ======================= 出欠回数管理 ====================== */
// 初期化またはlocalStorageから教科ごとの欠席回数を取得
function initializeAbsenceCount(subjectId) {
    let key = 'absenceCount_' + subjectId;  // 科目ごとのキーを設定
    let absenceCount = localStorage.getItem(key);

    //console.log("[process: main] cID:" + subjectId);
    // 欠席回数が存在しない場合は初期化
    if (absenceCount === null) {
        absenceCount = 0;
        localStorage.setItem(key, absenceCount);
        absenceCount = localStorage.getItem(key);
    }
    //console.log("[process: main] absenceCount:" + absenceCount);

    if (absenceCount) {
        // 欠席回数を画面に反映
        document.getElementById('absenceCount_' + subjectId).innerText = absenceCount;
    }
}

// 欠席ボタンが押された時の処理
function incrementAbsence(subjectId) {
    let key = 'absenceCount_' + subjectId;
    let absenceCount = parseInt(localStorage.getItem(key));

    // 欠席回数を1増やす
    absenceCount += 1;

    // localStorageに保存
    localStorage.setItem(key, absenceCount);

    // 画面の表示を更新
    document.getElementById('absenceCount_' + subjectId).innerText = absenceCount;
}

// ページ読み込み時に各教科の初期化
window.onload = function () {
    let subjectElements = document.querySelectorAll('[class ^="absenceButton_"]');
    if (subjectElements) {
        console.log("[process: main] sE:");
    }
    subjectElements.forEach(function (subjectElement) {
        let subjectId = subjectElement.dataset.subjectId;
        //console.log("[process: main] subjectid:" + subjectId);
        // 初期化
        initializeAbsenceCount(subjectId);

        // 欠席ボタンのイベントリスナーを設定
        document.getElementById('absenceButton_' + subjectId).addEventListener('click', function () {
            incrementAbsence(subjectId);
            console.log("[process: main] " + subjectId + " was registered.");
        });
    });
};
/* ========================================================== */

/* ===================== ハンバーガーメニュー ================= */
function toggleMenu() {
    var menu = document.getElementById("menu");
    if (menu.classList.contains("show")) {
        menu.classList.remove("show");
    } else {
        menu.classList.add("show");
    }
}
/* ========================================================== */

/* ===================== キャッシュバージョン ================= */
const phpV_send = document.getElementById('APPLICCATION_VERSION').textContent;
if (navigator.serviceWorker.controller) {
    navigator.serviceWorker.controller.postMessage({ type: 'PHP_APPLICCATION_VERSION', version: phpV_send });
}
/* ========================================================== */