<?php
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
 * main.php
 * 
 * main.php is the main file of RPRO app.
 */
define("APPLICCATION_VERSION", "v1.4.1");

/*
// POSTされたデータを取得
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JavaScriptから渡された値を取得
    $data_subjects = isset($_POST['jsData']) ? $_POST['jsData'] : '値がありません';
    $subjects = json_decode($data_subjects, true);
}
*/
?>

<!DOCTYPE html>
<html lang="ja">

<?php
require __DIR__ .  '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];
$dbName = $_ENV['DB_NAME'];
$dbPort = $_ENV['DB_PORT'];

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
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
    <meta name="google-site-verification" content="E3maZI8wva9G9nRwR8SETlWMM2MSqnCULOvfpkELHsI" />
</head>

<body>
    <?php echo '<div id="APPLICCATION_VERSION">APPLICCATION VERSION: ' . APPLICCATION_VERSION . '</div>' ?>
    <div id="DEBUG_MODE">DEBUG MODE: TRUE</div>
    <header>
        <div class="flex-byForce">
            <a class="header" href="/main.php">留年プロテクター <?php echo APPLICCATION_VERSION ?></a>
            <div class="menu-icon" id="menu-icon">&#9776;</div>
            <nav id="menu" class="menu">
                <ul>
                    <li><a href="help.php">よくある質問</a></li>
                    <li><button id="signup-btn">新規登録</button></li>
                    <li> <button id="delete-btn">削除</button></li>
                    <li><button id="install-btn">インストール</button></li>
                    <li> <button id="uninstall-btn">再起動</button></li>
                    <li id="debugmode">デバッグモード</li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="content">

        <div class="main">
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
                                                            echo '<option id ="cs-' . $row["ID"] . '" class="c-' . $row["学科ID"] . '">' . $row["科目名"] . '</option>';
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
                        <button id="finalize-btn">確定する</button>
                        <p id="result" style="display: none;"></p>
                    </div>
                </div>
            </div>
            <?php // ここまで新規登録画面
            ?>
            <div id="deletepopup-wrapper">
                <div id="popup-inside">
                    <div id="close">&times;</div>
                    <div id="message">
                        <p>本当に削除しますか?</p>
                        <button id="deletefinalize-btn">確定する</button>
                    </div>
                </div>
            </div>
            <div class="jikanwari">
                <?php
                // 曜日と時間割の枠データ
                $days = ['月', '火', '水', '木', '金'];
                $times = ['1', '2', '3', '4'];
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
                                <td class="time-cell asyncCNN"></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div>
                <?php
                /* 科目詳細表示 */
                foreach ($days as $index => $day) :
                    foreach ($times as $timeIndex) : ?>
                        <div class="asyncCD"></div>
                <?php endforeach;
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