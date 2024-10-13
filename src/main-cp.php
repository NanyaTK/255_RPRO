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
 * main-cp.php
 * 
 * main-cp.php is asynchronous processing file.
 */
define("APPLICCATION_VERSION", "v1.1.2");

// POSTされたデータを取得
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $data = explode(",", $data);
    $subjects = $data;
    foreach ($subjects as &$subject) {
        // cs- を削除
        $subject = substr($subject, 3);
    }

    // DBから科目データを取得
    require __DIR__ . '/../vendor/autoload.php';
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
    $result = $mysqli->prepare("
    SELECT
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
        `ID` DESC
    ");
    $result->execute();
    $time_schdule = $result->get_result();
    $row_data = $time_schdule->fetch_array(MYSQLI_NUM);

    $mysqli->close();
    // 科目取得ここまで

    $resSubjectsData = [];

    // ここから時間割表示
    if (!$subjects) {
        // POSTデータが無い時の時間割データの初期値
        $subjects = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
    }
    foreach ($subjects as &$subject) {
        $subject = substr($subject, 3);
    }
    // 曜日と時間割の初期データ
    $days = ['月', '火', '水', '木', '金'];
    $times = ['1', '2', '3', '4'];

    // 1週間の時間割の科目数（曜日数×時間数）
    $subjectsPerDay = count($subjects) / count($days);

    // 各曜日ごとに科目を分割
    $subjectsByDay = array_chunk($subjects, $subjectsPerDay);

    $howmanyA = 0;
    foreach ($days as $index => $day):
        foreach ($times as $timeIndex):
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
                $subjectId = $row["ID"]; // IDを使う  
                $maxabsent = $row["最大欠席可能回数"]; //　最大欠席回数を取得する
                $subjectType = $row["科目分類"];
                $subjectTypeClass = "open-popup-btn-";
                if ($subjectType == "専門") {
                    $subjectTypeClass .= "purple-";
                } else //一般科目
                {
                    $colorName = "";
                    switch ($subjectType) {
                        case "一般赤":
                            $colorName .= "red-";
                            break;
                        case "一般水":
                            $colorName .= "blue-";
                            break;
                        case "一般黄":
                            $colorName .= "yellow-";
                            break;
                        case "一般桃":
                            $colorName .= "pink-";
                            break;
                        default:
                            $colorName .= "green-";
                            break;
                    }
                    $subjectTypeClass .= $colorName;
                }
                $resSubjectsData[$howmanyA] = <<<'EOD'
                echo ('<button class ="' . $subjectTypeClass . $howmanyA . ' subject" data-subject-id=' . $subjectId . '>');
                echo ($row["科目名"]);
                echo "</button>";'
                EOD;
                if ($maxabsent) {
                    $resSubjectsData[$howmanyA] .= <<<'EOD'
                    echo '<p> <span id="absenceCount_' . $howmanyA . '">0</span> / ' . $maxabsent . '</p>';
                    EOD;
                } else {
                    $resSubjectsData[$howmanyA] .= <<<'EOD'
                    echo '<p style="font-size: x-large;">特殊欠席条件</p>';
                    echo '<p> <span id="absenceCount_' . $howmanyA . '" class="unvisible">0</span>  ' . $maxabsent . '</p>';
                    EOD;
                }
            } else {
                $resSubjectsData[$howmanyA] .= <<<'EOD'
                echo ('<button style="display:none;" class ="open-popup-btn-green-' . $howmanyA . ' subject" data-subject-id=' . $subjectId . '>');
                echo isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                EOD;
                $subjectName = isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                $subjectId = $index . '-' . $timeIndex; // 科目IDがない場合はデフォルトのIDを作る
                $maxabsent = 0; //　時間割に設定していないマスは0を表示
                $resSubjectsData[$howmanyA] .= 'echo "</button>";';
            }
            // resSubjectsData[0] = "<button> ~~ </button><p> ~~~ </p>"
            // resSubjectsData[19] までできる
            $howmanyA += 1;
        endforeach;
    endforeach;

    $response = json_encode($resSubjectsData);
    echo $response;
}