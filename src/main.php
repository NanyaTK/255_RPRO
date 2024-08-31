<?php
// POSTされたデータを取得
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JavaScriptから渡された値を取得
    $data_subjects = isset($_POST['jsData']) ? $_POST['jsData'] : '値がありません';
    $subjects = json_decode($data_subjects, true);
}
?>

<!DOCTYPE html>
<html lang="ja">

<?php
$mysqli = new mysqli("127.0.0.1","rpro_u","uhe6WTScplbJ","rpro",3306);
if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit();
} else {
    $mysqli->set_charset("utf8");
}
$mysqli->query("use rpro");
$result = $mysqli->prepare(
"SELECT
    `ID`
    ,`科目ID`
    , `学科ID`
    , `科目名`
    , `講義回数`
    , `最大欠席可能回数`
    , `特殊欠席条件`
    , `評価割合`
    , `科目分類`
FROM
    rpro.classtable
ORDER BY
    `ID` DESC");
$result->execute();

$time_schdule = $result->get_result();

$row_data = $time_schdule->fetch_array(MYSQLI_NUM);

$mysqli->close();
?>

<head>
    <meta charset="UTF-8" />
    <title>留年プロテクター</title>
    <link href="main.css" rel="stylesheet">
</head>

<body>
    <div class="content">
        <header>
            <a class="header" href="/">留年プロテクター</a>
        </header>

        <div class="main">
            <div class="empty"></div>
            <button id="signup-btn">新規登録</button>
            <!-- ここから新規登録画面 -->
            <div id="popup-wrapper">
                <div id="popup-inside">
                    <div id="close">x</div>
                    <div id="message">
                        <h2>新規登録</h2>
                        <p>時間割を設定してください</p>
                            <label class = "select-class">
                                <select>
                                    <option id = 13>電気情報工学科電気電子コース4年</option>
                                    <option id = 14>電気情報工学科情報工学コース4年</option>
                                    <option id = 17>電気情報工学科電気電子コース5年</option>
                                    <option id = 18>電気情報工学科情報工学コース5年</option>
                                </select>
                            </label>
                        <div class="jikanwari">
                            <?php
                            // 曜日と時間割の初期データ
                            $days = ['月', '火', '水', '木', '金'];
                            $times = ['1', '2', '3', '4'];
                            ?>
                            <table class="timetable-signup">
                                <tr>
                                    <th class="day-column">曜日</th>
                                    <?php foreach ($times as $time) : ?>
                                        <th><?php echo $time; ?></th>
                                    <?php endforeach; ?>
                                </tr>
                                <?php foreach ($days as $day) : ?>
                                    <tr>
                                        <td class="day-column"><?php echo $day; ?></td>
                                        <?php foreach ($times as $timeIndex => $time) : ?>
                                            <td class="time-cell">
                                                <label class="select-subject">
                                                    <?php
                                                    // selectタグのidを動的に生成
                                                    $selectId = 'mys-' . $day . '-' . $timeIndex;
                                                    ?>
                                                    <select id="<?php echo $selectId; ?>" class="subject-select">
                                                        <option>空コマ</option>
                                                        <?php
                                                        for($row_no = $time_schdule->num_rows - 1; $row_no >= 0; $row_no--){
                                                            $time_schdule->data_seek($row_no);
                                                            $row = $time_schdule->fetch_assoc();
                                                            echo '<option id = '.$row["ID"].'>'.$row["科目名"].'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </label>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <form method="POST" action="main.php" id="hiddenForm">
                        <!-- JSで値を設定する隠しフィールド -->
                        <input type="hidden" name="jsData" id="jsData">
                        <button id="finalize-btn" onclick="getAllSelectedOptionIds()" type = "submit">確定する</button>
                        </form>
                        <p id="result"></p>

                        <p id="result"></p>
                        <script>
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
                                
                                for(let i = 0; i < selectedOptionIds.length; i++){
                                    const registData = selectedOptionIds[i];
                                    registDatas.push(registData);
                                }
                                                           
                                console.log(registDatas);
                                const registJSON = JSON.stringify(registDatas);
                                localStorage.setItem('key',registJSON);
                                let getval = localStorage.getItem('key');
                                let getData = JSON.parse(getval);
                                console.log(getData);

                                // JSONデータを文字列にして隠しフィールドにセット
                                document.getElementById('jsData').value = JSON.stringify(getData);
                                // 必要に応じてフォームを自動送信する
                                // document.getElementById('hiddenForm').submit();
                                location.reload();
                            }
                        </script>
                    </div>
                </div>
            </div>
            <!-- ここまで新規登録画面 -->
            <!-- ここから時間割表示　 -->
            <div class="jikanwari">
                <?php
                if(!$subjects){
                    $subjects = ["","","","","","","","","","","","","","","","","","","",""];
                }
                // 曜日と時間割の初期データ
                $days = ['月', '火', '水', '木', '金'];
                $times = ['1', '2', '3', '4'];

                // 1週間の時間割の科目数（曜日数×時間数）
                $subjectsPerDay = count($subjects) / count($days);

                // 各曜日ごとに科目を分割
                $subjectsByDay = array_chunk($subjects, $subjectsPerDay);
                ?>
                <script>
                    console.log('subjects finish');
                </script>
                <table class="timetable">
                    <tr>
                        <th class="day-column">曜日</th>
                        <?php foreach ($times as $time) : ?>
                            <th><?php echo $time; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <?php foreach ($days as $index =>$day) : ?>
                        <tr>
                            <td class="day-column"><?php echo $day; ?></td>
                            <?php foreach ($times as $timeIndex) : ?>
                                <td class="time-cell">
                                    <!-- ここで科目設定 -->
                                    <?php
                                    if($subjectsByDay[$index][$timeIndex - 1]){
                                        if($subjectsByDay[$index][$timeIndex - 1] == 1){
                                            // 国語IVのid対策用
                                            $row_no = $time_schdule->num_rows - $subjectsByDay[$index][$timeIndex - 1];
                                        }else{
                                            $row_no = $time_schdule->num_rows - $subjectsByDay[$index][$timeIndex - 1] + 1;
                                        }
                                        $time_schdule->data_seek($row_no);
                                        $row = $time_schdule->fetch_assoc();
                                        echo ($row["科目名"]);
                                    }else
                                        echo isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                                    ?>
                                    <!-- ここまで科目設定 -->
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <!-- ここまで時間割表示　 -->
        <footer>
            &copy; 2024 留年プロテクタープロジェクト
        </footer>
    </div>

    <script>
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
    </script>

</body>

</html>