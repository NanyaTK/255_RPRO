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

let DEVFLAG = false;
if ((hostname === 'rpro.nanyatk.com' || hostname === 'test.rpro.nanyatk.com') && environment === 'dev') {
    DEVFLAG = true;
} else if (hostname === 'rpro.nanyatk.com' || hostname === 'test.rpro.nanyatk.com') {
    DEVFLAG = false;
} else {
    DEVFLAG = true;
}
const dbgmd = document.getElementById("debugmode");
dbgmd.innerText = "DEBUG MODE: " + DEVFLAG;
let consoleStyle = "font-size:x-large";
console.log("%c[process: main] DEBUG MODE: %c" + DEVFLAG, consoleStyle, consoleStyle);
if (DEVFLAG) {
    const APPV = document.getElementById("APPLICCATION_VERSION");
    APPV.style.display = "block";
    const DBGM = document.getElementById("DEBUG_MODE");
    DBGM.style.display = "block";
}

/* =========== service Worker 新規インストールイベント ============ */
const registerServiceWorker = async () => {
    if ("serviceWorker" in navigator) {
        try {
            const registration = await navigator.serviceWorker.register("./sw.js", {
                scope: "/",
            });
            if (registration.installing) {
                if (DEVFLAG) {
                    console.log("[process: main] Service worker installing");
                }
            } else if (registration.waiting) {
                if (DEVFLAG) {
                    console.log("[process: main] Service worker installed");
                }
            } else if (registration.active) {
                if (DEVFLAG) {
                    console.log("[process: main] Service worker active");
                }
            }
        } catch (error) {
            console.error(`[process: main] Registration failed with ${error}`);
            alert("[process: main -> SW] Registration failed");
        }
    }
}
/* ============================================================== */

/* ===================== ハンバーガーメニュー ================= */
function toggleMenu() {
    var menu = document.getElementById("menu");
    if (menu.classList.contains("show")) {
        menu.classList.remove("show");
    } else {
        menu.classList.add("show");
    }
}
const showMenu = document.getElementById("menu-icon");
showMenu.addEventListener("click", toggleMenu);
/* ========================================================== */

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
    alert("[process: main] Pre-reload process completed.\nReloading now.")
    window.location.reload();
});
/* ============================================================== */

/* ==================== 新規登録ボタンイベント ==================== */
const signUpBtn = document.getElementById('signup-btn');
const popupWrapper = document.getElementById('popup-wrapper');
const close = document.getElementById('close');
const registState = localStorage.getItem("deleteflag");

const registDataCheck = localStorage.getItem('key');
let tmp = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
tmp = JSON.stringify(tmp);
if (registDataCheck === tmp) {
    signUpBtn.style.display = "block"
} else if (registDataCheck) {
    signUpBtn.style.display = "none"
} else {
    signUpBtn.style.display = "block"
}

// ボタンをクリックしたときにポップアップを表示させる
signUpBtn.addEventListener('click', () => {
    popupWrapper.style.display = "block";
    toggleMenu();
});

// ポップアップの外側又は「x」のマークをクリックしたときポップアップを閉じる
popupWrapper.addEventListener('click', e => {
    if (e.target.id === popupWrapper.id || e.target.id === close.id) {
        popupWrapper.style.display = 'none';
    }
});
/* ============================================================== */


/* ================== 新規登録確定ボタンイベント ================== */
function updateClassTable() {
    const openButtons = document.querySelectorAll('[class ^="open-popup-btn"]');
    const overlays = document.querySelectorAll('[class ^="overlay-absent"]');
    const closeButtons = document.querySelectorAll('close-absent');
    if (DEVFLAG) {
        console.log("[process: main] " + openButtons)
    }
    function showPopup() {
        overlays[this.num].style.display = 'block';
    }
    function hidePopup() {
        overlays.forEach(overlay => {
            overlay.style.display = 'none';
        });
    }
    let i = 0;
    openButtons.forEach(button => {
        button.removeEventListener('click', showPopup);
    });
    closeButtons.forEach(closeButton => {
        closeButton.removeEventListener('click', hidePopup);
    });
    overlays.forEach(overlay => {
        overlay.removeEventListener('click', hidePopup);
    });

    let subjectElements = document.querySelectorAll('[class ^="absenceButton_"]');
    if (subjectElements) {
        if (DEVFLAG) {
            console.log("[process: main] sE: update");
        }
    }
    subjectElements.forEach(function (subjectElement) {
        // 欠席ボタンのイベントリスナーを設定
        subjectElement.removeEventListener('click', function () { });
    });

    return new Promise((resolve) => {
        let getval = localStorage.getItem('key');
        if (getval) {
            let getData = getval.replace(/[\[\]]/g, '');
            if (DEVFLAG) {
                console.log("[process: main] " + getData);
            }
            getData = JSON.stringify(getData);
            if (DEVFLAG) {
                console.log("[process: main] " + getData)
            }
            fetch('/main-cp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: getData // 必要なデータを送信
            })
                .then(response => response.json())
                .then(data => {
                    if (DEVFLAG) {
                        console.log('[process: main-cp] ', data);
                    }
                    if (data) {
                        (async () => {
                            let [subjectsData, subjectsDetail] = data;
                            const oldDataTag = document.getElementsByClassName("asyncCNN");
                            for (let i = 0; i < 20; i++) {
                                oldDataTag[i].innerHTML = subjectsData[i];
                            }
                            const oldDetailTag = document.getElementsByClassName("asyncCD");
                            for (let i = 0; i < 20; i++) {
                                oldDetailTag[i].innerHTML = subjectsDetail[i];
                            }

                            await new Promise(() => {
                                const openButtons = document.querySelectorAll('[class ^="open-popup-btn"]');
                                const overlays = document.querySelectorAll('[class ^="overlay-absent"]');
                                const closeButtons = document.querySelectorAll('close-absent');
                                if (DEVFLAG) {
                                    console.log("[process: main] " + openButtons)
                                }
                                function showPopup() {
                                    overlays[this.num].style.display = 'block';
                                }
                                function hidePopup() {
                                    overlays.forEach(overlay => {
                                        overlay.style.display = 'none';
                                    });
                                }
                                let i = 0;
                                openButtons.forEach(button => {
                                    button.addEventListener('click', { num: i++, handleEvent: showPopup });
                                });
                                closeButtons.forEach(closeButton => {
                                    closeButton.addEventListener('click', hidePopup);
                                });
                                overlays.forEach(overlay => {
                                    overlay.addEventListener('click', hidePopup);
                                });

                                function incrementAbsence(subjectId) {
                                    let key = 'absenceCount_' + subjectId;
                                    let absenceCount = parseInt(localStorage.getItem(key));
                                    let counters = document.querySelectorAll(`.absenceCount_[data-absent-id="${subjectId}"]`);
                                    absenceCount += 1;
                                    localStorage.setItem(key, absenceCount)
                                    counters.forEach(counter => {
                                        counter.innerText = absenceCount; // 値を更新
                                    })
                                    //document.getElementById('absenceCount_' + subjectId).innerText = absenceCount;
                                }
                                let subjectElements = document.querySelectorAll('button[datasubjectid]');
                                if (subjectElements) {
                                    if (DEVFLAG) {
                                        console.log("[process: main] sE: updating...");
                                        subjectElements.forEach(sub => {
                                            console.log(sub);
                                        })
                                    }
                                }
                                subjectElements.forEach(function (subjectElement) {
                                    let subjectId = subjectElement.dataset.subjectId;
                                    initializeAbsenceCount(subjectId);
                                    //document.querySelectorAll(".absenceButton_" + subjectId).addEventListener('click', function () {
                                    subjectElement.addEventListener('click', function () {
                                        incrementAbsence(subjectId);
                                        if (DEVFLAG) {
                                            console.log("[process: main] subjectDstNum: " + subjectId + " was registered.");
                                        }
                                    });
                                });
                                if (DEVFLAG) {
                                    console.log("[process: main] sE: update finished");
                                }
                            });
                        })();
                    }
                })
                .catch(error => {
                    console.error('[process: main-cp] ', error);
                }).then(() => {
                    popupWrapper.style.display = 'none';
                });
        }
        const registDataCheck = localStorage.getItem('key');
        let tmp = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
        tmp = JSON.stringify(tmp);
        if (registDataCheck === tmp) {
            signUpBtn.style.display = "block"
            DeleteBtn.style.display = "none"
        } else if (registDataCheck) {
            signUpBtn.style.display = "none"
            DeleteBtn.style.display = "block"
        } else {
            signUpBtn.style.display = "block"
            DeleteBtn.style.display = "none"
        }
        resolve();
    });
}

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
    if (DEVFLAG) {
        console.log(selectedOptionIds);
    }
    const registDatas = [];

    for (let i = 0; i < selectedOptionIds.length; i++) {
        const registData = selectedOptionIds[i];
        registDatas.push(registData);
    }

    const registJSON = JSON.stringify(registDatas);
    localStorage.setItem('key', registJSON);

    updateClassTable().then(() => {
        if (DEVFLAG) {
            console.log("[process: main] classTable updated");
        }
    })
}

const RegistBtn = document.getElementById("finalize-btn");
RegistBtn.addEventListener('click', () => { getAllSelectedOptionIds(); })
/* ============================================================== */

/* ====================== 削除ボタンイベント ====================== */
const DeleteBtn = document.getElementById('delete-btn');
const DeletePopupWrapper = document.getElementById('deletepopup-wrapper');
const DeleteClose = document.getElementById('close');

if (registDataCheck === tmp) {
    DeleteBtn.style.display = "none"
} else if (registDataCheck) {
    DeleteBtn.style.display = "block"
} else {
    DeleteBtn.style.display = "none"
}

// ボタンをクリックしたときにポップアップを表示させる
DeleteBtn.addEventListener('click', () => {
    DeletePopupWrapper.style.display = "block";
    toggleMenu();
});

// ポップアップの外側又は「x」のマークをクリックしたときポップアップを閉じる
DeletePopupWrapper.addEventListener('click', e => {
    if (e.target.id === DeletePopupWrapper.id || e.target.id === close.id) {
        DeletePopupWrapper.style.display = 'none';
    }
});
/* ============================================================== */

/* =================== 削除確定ボタンイベント ===================== */
const DeleteFinalBtn = document.getElementById('deletefinalize-btn');
DeleteFinalBtn.addEventListener('click', () => {
    const registDatas = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
    const registJSON = JSON.stringify(registDatas);
    localStorage.setItem('key', registJSON);
    let i = 0;
    let subjectElements = document.querySelectorAll('[datasubjectid]');
    updateClassTable().then(() => {
        if (DEVFLAG) {
            console.log("[process: main] classTable updated");
        }
        subjectElements.forEach(function (subjectElement) {
            i = subjectElement.dataset.subjectId;
            let key = 'absenceCount_' + i;  // 科目ごとのキーを設定
            localStorage.removeItem(key);
            console.log(key)
        });
        if (DEVFLAG) {
            console.log("[process: main] absenceCount updated");
        }
    })
    DeletePopupWrapper.style.display = "none";
    alert("[process: main] Data deleted")
})
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
    const CTData = selectedClassId + "," + selectedTermId;
    if (DEVFLAG) {
        console.log("[process: main] " + CTData);
    }
    FilterClasses(selectedClassId);
    ableRstFlag = true;
    if (DEVFLAG) {
        console.log("[process: main] filtering finished.");
    }
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
        if (DEVFLAG) {
            console.log("[process: main] filtering reseted.");
        }
    } else {
        ableRstFlag = false;
        if (DEVFLAG) {
            console.log("[process: main] filtering was not reseted.");
        }
    }
}

let tempOptions = {};
let temp = {};
let ableRstFlag = false;
const cltempBtn = document.getElementById("cltemp-btn");
cltempBtn.addEventListener("click", () => {
    const CTData = AutoCompleteClasses();
    fetch('/asyncSW.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(CTData) // 必要なデータを送信
    })
        .then(response => response.json())
        .then(data => {
            if (DEVFLAG) {
                console.log('[process: asyncSW] ', data);
            }
            if (data) {
                let clID = data.split(',');
                if (DEVFLAG) {
                    console.log('[process: asyncSW] ', clID);
                }
                const classElements = document.querySelectorAll(".subject-select");
                classElements.forEach((classElement) => {
                    if (DEVFLAG) {
                        //console.log(clID[0]);
                    }
                    if (clID[0] != 0) {
                        if (DEVFLAG) {
                            //console.log("cs-" + (clID[0]));
                        }
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

/* ==================== 時間割ポップアップ関連 ==================== */
document.addEventListener('DOMContentLoaded', () => {
    const openButtons = document.querySelectorAll('[class ^="open-popup-btn"]');
    const overlays = document.querySelectorAll('[class ^="overlay-absent"]');
    const closeButtons = document.querySelectorAll('close-absent');

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
/* ========================================================== */

/* ======================= 出欠回数管理 ====================== */
// 初期化またはlocalStorageから教科ごとの欠席回数を取得
function initializeAbsenceCount(subjectId) {
    let key = 'absenceCount_' + subjectId;  // 科目ごとのキーを設定
    let absenceCount = localStorage.getItem(key);
    let counters = document.querySelectorAll(`.absenceCount_[data-absent-id="${subjectId}"]`);
    if (DEVFLAG) {
        console.log("[process: main] cID:" + subjectId);
    }
    // 欠席回数が存在しない場合は初期化
    if (absenceCount === null) {
        absenceCount = 0;
        localStorage.setItem(key, absenceCount);
        absenceCount = localStorage.getItem(key);
    }
    if (DEVFLAG) {
        console.log("[process: main] absenceCount:" + absenceCount);
    }

    if (absenceCount) {
        // 欠席回数を画面に反映
        counters.forEach(counter => {
            counter.innerText = absenceCount; // 値を更新
        })
        //document.getElementById('absenceCount_' + subjectId).innerText = absenceCount;
    }
}

// 欠席ボタンが押された時の処理
function incrementAbsence(subjectId) {
    let key = 'absenceCount_' + subjectId;
    let absenceCount = parseInt(localStorage.getItem(key));
    let counters = document.querySelectorAll(`.absenceCount_[data-absent-id="${subjectId}"]`);
    // 欠席回数を1増やす
    absenceCount += 1;
    // localStorageに保存
    localStorage.setItem(key, absenceCount);
    // 画面の表示を更新
    counters.forEach(counter => {
        counter.innerText = absenceCount; // 値を更新
    })
    //document.getElementById('absenceCount_' + subjectId).innerText = absenceCount;
}

// ページ読み込み時に各教科の初期化
function initializeAConload() {
    let subjectElements = document.querySelectorAll('button[datasubjectid]');
    if (subjectElements) {
        if (DEVFLAG) {
            console.log("[process: main] sE:622");
        }
    }
    subjectElements.forEach(function (subjectElement) {
        let subjectId = subjectElement.dataset.subjectId;
        if (DEVFLAG) {
            console.log("[process: main] subjectid:" + subjectId);
        }
        // 初期化
        initializeAbsenceCount(subjectId);

        // 欠席ボタンのイベントリスナーを設定
        //document.querySelectorAll(".absenceButton_" + subjectId).addEventListener('click', function () {
        subjectElement.addEventListener('click', function () {
            incrementAbsence(subjectId);
            if (DEVFLAG) {
                console.log("[process: main] subjectDstNum: " + subjectId + " was registered.");
            }
        });
    });
}
/* ========================================================== */

/* ===================== キャッシュバージョン ================= */
const phpV_send = document.getElementById('APPLICCATION_VERSION').textContent;
if (navigator.serviceWorker.controller) {
    navigator.serviceWorker.controller.postMessage({ type: 'PHP_APPLICCATION_VERSION', version: phpV_send });
}
/* ========================================================== */

/* ==================== ページ読み込み時の処理 ================ */
window.onload = async function () {
    await registerServiceWorker();
    if (DEVFLAG) {
        console.log("[process: main] processing onload method...");
    }
    initializeAConload();
    updateClassTable().then(() => {
        if (DEVFLAG) {
            console.log("[process: main] classTable updated");
        }
    })
    if (DEVFLAG) {
        console.log("[process: main] processing onload method finished");
    }
};
/* ========================================================== */
