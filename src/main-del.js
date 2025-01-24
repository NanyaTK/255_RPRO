
async function delCacheAndSW() {
    
}
const delBtn = document.getElementById("delete-btn")
delBtn.addEventListener('click', () => {
    const delLocalstrage = new Promise((resolve, reject) => {
        for (let i = 0; i <= 75; i++) {
            localStorage.removeItem(`absenceCount_${i}`);
        }
        localStorage.removeItem('classDataCache');
        localStorage.removeItem('classDetailCache');
        localStorage.removeItem('deleteCacheFLAG');
        //localStorage.removeItem('key');
        if (localStorage.length > 0) {
            reject("error. key detected.")
        } else {
            resolve("key deleted.")
        }
    })
    delLocalstrage.then((message) => {
        alert(message + "ローカルストレージのデータを正常に削除しました．\nキャッシュ削除を開始します．\n101 : " + message)
      
    }).catch((err) => {
        alert("ローカルストレージの削除に失敗しました．\n処理を中断します．\n102 : " + err)
    })
})

