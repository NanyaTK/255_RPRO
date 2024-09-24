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
                        <div class="flex-byForce">
                            <label class="select-class">
                                <select class="auto-complete">
                                    <option id=13>電気情報工学科電気電子コース4年</option>
                                    <option id=14>電気情報工学科情報工学コース4年</option>
                                    <option id=17>電気情報工学科電気電子コース5年</option>
                                    <option id=18>電気情報工学科情報工学コース5年</option>
                                </select>
                            </label>
                            <label class="select-class term-sel-label">
                                <select class="term-sel">
                                    <option id=1>前期</option>
                                    <option id=2>後期</option>
                                </select>
                            </label>
                            <button id="cltemp-btn" class="clt-fil-btn filDB">絞り込み</button>
                            <button id="rstFilter-btn" class="clt-fil-btn">リセット</button>
                        </div>
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
                                                        <option class="empty">空コマ</option>
                                                        <?php
                                                        for ($row_no = $time_schdule->num_rows - 1; $row_no >= 0; $row_no--) {
                                                            $time_schdule->data_seek($row_no);
                                                            $row = $time_schdule->fetch_assoc();
                                                            echo '<option id ="' . $row["ID"] . '" class="c-' . $row["学科ID"] . '">' . $row["科目名"] . '</option>';
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
                        <?php
                        $howmanyA = 0;
                        $howmanyB = 0;
                        foreach ($times as $time) : ?>
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
                                        if ($subjectsByDay[$index][$timeIndex - 1] == 1) {
                                            $row_no = $time_schdule->num_rows - $subjectsByDay[$index][$timeIndex - 1];
                                        } else {
                                            $row_no = $time_schdule->num_rows - $subjectsByDay[$index][$timeIndex - 1] + 1;
                                        }
                                        $time_schdule->data_seek($row_no);
                                        $row = $time_schdule->fetch_assoc();
                                        $subjectName = $row["科目名"];
                                        $subjectId = $row["科目ID"]; // 科目ごとのIDを使う  
                                        echo ('<button id="absenceButton_' . $subjectId . '" class ="open-popup-btn-' . $howmanyA . ' subject" data-subject-id=' . $subjectId . '>');
                                        $howmanyA += 1;
                                        echo ($row["科目名"]);
                                        $subjectName = $row["科目名"];
                                        $subjectId = $row["科目ID"]; // 科目ごとのIDを使う
                                    } else {
                                        echo ('<button class ="open-popup-btn-' . $howmanyA . ' subject" data-subject-id=' . $subjectId . '>');
                                        $howmanyA += 1;
                                        echo isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                                        $subjectName = isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                                        $subjectId = $index . '-' . $timeIndex; // 科目IDがない場合はデフォルトのIDを作る
                                    }
                                    ?>
                                    </button>
                                    <p>欠席回数 <span id="absenceCount_<?php echo $subjectId; ?>">0</span> / 最大欠席回数</p>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div>
                <?php
                /* こちらを採用 */
                foreach ($days as $index => $day) :
                    foreach ($times as $timeIndex) :
                        echo ('<div class="overlay-absent-' . $howmanyB . '" id="overlay-absent">');

                        echo (' <div class="popup-absent" id="popup-absent">
                    <p class="close-absent" id="close-absent">&times;</p>
                    <div class="sm-w">
                        <p class="name-subjects">');

                        //各要素に適するRowを設定
                        if ($subjectsByDay[$index][$timeIndex - 1]) {
                            if ($subjectsByDay[$index][$timeIndex - 1] == 1) {
                                // 国語IVのid対策用
                                $row_no = $time_schdule->num_rows - $subjectsByDay[$index][$timeIndex - 1];
                            } else {
                                $row_no = $time_schdule->num_rows - $subjectsByDay[$index][$timeIndex - 1] + 1;
                            }
                            $time_schdule->data_seek($row_no);
                            $row = $time_schdule->fetch_assoc();

                            echo $row["科目名"];
                            echo ('</p>
                                    <p class="teacher-subjects">');
                            echo ("担当教員" . "：" . $row["学科ID"]);
                            echo ("</p>");
                            if (!empty($row["特殊欠席条件"])) {
                                echo '<p class = "absent-condition">' . $row["特殊欠席条件"] . '</p>';
                            } else {
                                echo '<p class = "absent-condition">特殊欠席条件はありません</p>';
                            }
                            echo '<div class="scroll"><table class="rating-subjects">' . $row["評価割合"] . '</table></div>';
                            echo ("</p>");
                            echo ('<p class="absent-msg">本当に欠席しますか？</p>');
                            echo ('<button class="absenceButton_' . $howmanyB . ' absent-btn" data-subject-id=' . $howmanyB . '>欠席する</button>');
                        } else {
                            echo '<p class="name-subjects">開きコマです</p>';
                            echo '<p class="name-subjects">ゆっくりお休みください</p>';
                        }

                        echo ('</div>');
                        echo ('</div>');
                        echo ('</div>');
                        $howmanyB += 1;
                    endforeach;
                endforeach;
                ?>
            </div>
        </div>
        <?php // ここまで時間割表示   
        ?>
    </div>
    <script type="text/javascript" src="main.js"></script>
    </div>
    <footer>
        &copy; 2024 留年プロテクタープロジェクト
    </footer>
</body>

</html>