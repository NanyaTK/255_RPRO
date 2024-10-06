<?php
define("APPLICCATION_VERSION", "v1.3.1");

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
$mysqli = new mysqli("127.0.0.1", "rpro_u", "uhe6WTScplbJ", "rpro", 3306);
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
    `ID` DESC"
);
$result->execute();
$time_schdule = $result->get_result();
$row_data = $time_schdule->fetch_array(MYSQLI_NUM);

$mysqli->close();
?>

<head>
    <meta charset="UTF-8" />
    <title>留年プロテクター</title>
    <link rel="icon" href="/icon-images/favicon.ico">
    <link rel="stylesheet" href="main.css" />
    <script type="text/javascript" src="sw.js"></script>
    <link rel="manifest" href="mainManifest.json" />
</head>

<body>
    <header>
        <a class="header" href="/">留年プロテクター</a>
    </header>
    <div class="content">

        <div class="main">
            <div class="flex-byForce">
                <button id="signup-btn">新規登録</button>
                <button id="install-btn">インストール</button>
                <button id="uninstall-btn">再起動</button>
            </div>
            <?php // ここから新規登録画面
            ?>
            <div id="popup-wrapper">
                <div id="popup-inside">
                    <div id="close">&times;</div>
                    <div id="message">
                        <h2>新規登録</h2>
                        <p>時間割を設定してください</p>
                        <label class="select-class">
                            <select>
                                <option id=13>電気情報工学科電気電子コース4年</option>
                                <option id=14>電気情報工学科情報工学コース4年</option>
                                <option id=17>電気情報工学科電気電子コース5年</option>
                                <option id=18>電気情報工学科情報工学コース5年</option>
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
                                                        for ($row_no = $time_schdule->num_rows - 1; $row_no >= 0; $row_no--) {
                                                            $time_schdule->data_seek($row_no);
                                                            $row = $time_schdule->fetch_assoc();
                                                            echo '<option id = ' . $row["ID"] . '>' . $row["科目名"] . '</option>';
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
                            <?php // JSで値を設定する隠しフィールド
                            ?>
                            <input type="hidden" name="jsData" id="jsData">
                            <button id="finalize-btn" onclick="getAllSelectedOptionIds()" type="submit">確定する</button>
                        </form>
                        <p id="result"></p>
                    </div>
                </div>
            </div>
            <?php // ここまで新規登録画面
            ?>
            <div class="jikanwari">
                <?php
                // ここから時間割表示
                if (!$subjects) {
                    // POSTデータが無い時の時間割データの初期値
                    $subjects = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
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
                    console.log('[process: main] subjects finish');
                </script>
                <table class="timetable">
                    <tr>
                        <th class="day-column">曜日</th>
                        <?php foreach ($times as $time) : ?>
                            <th><?php echo $time; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <?php
                    foreach ($days as $index => $day) : ?>
                        <tr>
                            <td class="day-column"><?php echo $day; ?></td>
                            <?php foreach ($times as $timeIndex) : ?>
                                <td class="time-cell">
                                    <?php
                                    // 科目名を取得
                                    if ($subjectsByDay[$index][$timeIndex - 1]) {
                                        $row_no = $time_schdule->num_rows - $subjectsByDay[$index][$timeIndex - 1] + 1;
                                        $time_schdule->data_seek($row_no);
                                        $row = $time_schdule->fetch_assoc();
                                        $subjectName = $row["科目名"];
                                        $subjectId = $row["ID"]; // 科目ごとのIDを使う
                                        $maxabsent = $row["最大欠席可能回数"]; //　最大欠席回数を取得する
                                    } else {
                                        $subjectName = isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                                        $subjectId = $index . '-' . $timeIndex; // 科目IDがない場合はデフォルトのIDを作る
                                        $maxabsent = 0; //　時間割に設定していないマスは0を表示
                                    }
                                    ?>

                                    <!-- 欠席ボタンと欠席回数の表示 -->
                                    <button id="absenceButton_<?php echo $subjectId; ?>" class="open-popup-btn subject" data-subject-id="<?php echo $subjectId; ?>">
                                        <?php echo $subjectName; ?>
                                    </button>
                                    <p><span id="absenceCount_<?php echo $subjectId; ?>">0</span> / <?php echo $maxabsent; ?></p>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="overlay-absent" id="overlay-absent">
                <div class="popup-absent" id="popup-absent">
                    <p class="close-absent" id="close-absent">&times;</p>
                    <div class="sm-w">
                        <p class="name-subjects"><?php echo $row["科目名"]; ?></p>
                        <p class="teacher-subjects"><?php echo ("担当教員" . "：" . $row["学科ID"]); ?></p>
                        <?php
                        if (!empty($row["特殊欠席条件"])) {
                            echo '<p class = "absent-condition">.$row["特殊欠席条件"].</p>';
                        } else {
                            echo '<p class = "absent-condition">特殊欠席条件はありません</p>';
                        }
                        echo '<div class="scroll"><table class="rating-subjects">' . $row["評価割合"] . '</table></div>';
                        ?>
                        <p class="absent-msg">本当に欠席しますか？</p>
                        <button class="absent-btn" id="absenceButton">欠席する</button>
                    </div>
                </div>
            </div>
            <?php // ここまで時間割表示   
            ?>
            <script type="text/javascript" src="main.js"></script>
        </div>

    </div>
    <footer>
        &copy; 2024 留年プロテクタープロジェクト
    </footer>
</body>

</html>