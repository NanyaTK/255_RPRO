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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $data = str_replace('"', '', $data);
    $data = explode(',', $data);
    //$data = explode(",", $data);
    //echo json_encode($data[0]);
    $subjects = $data;

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
    $resSubjectsDetail = [];

    $subjectIDs = [];

    // ここから時間割表示
    if (!$subjects) {
        // POSTデータが無い時の時間割データの初期値
        $subjects = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
    }
    foreach ($subjects as &$subject) {
        // cs- を削除
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
    $howmanyB = 0;

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
                $subjectIDs[$howmanyA] = $subjectId;
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
                $resSubjectsData[$howmanyA] .= <<<EOD
                <button class ='$subjectTypeClass$howmanyA subject' data-subject-id='$subjectId'>{$row["科目名"]}</button>
                EOD;
                if ($maxabsent) {
                    $resSubjectsData[$howmanyA] .= <<<EOD
                    <p> <span id = $howmanyA class ='absenceCount_' data-absent-id='$subjectId'>0</span> / $maxabsent</p>
                    EOD;
                    #<p> <span id='absenceCount_$subjectId'>0</span> / $maxabsent</p>
                    
                } else {
                    $resSubjectsData[$howmanyA] .= <<<EOD
                    <p style='font-size: x-large;'>特殊欠席条件</p>
                    EOD;
                    $resSubjectsData[$howmanyA] .= <<<EOD
                    <p> <span class='unvisible'>0</span>  $maxabsent</p>
                    EOD;
                    #<p> <span id='absenceCount_$subjectId' class='unvisible'>0</span>  $maxabsent</p>
                }
            } else {
                $resSubjectsData[$howmanyA] .= <<<EOD
                <button style='display:none;' class ='open-popup-btn-green-$howmanyA subject datasubjectid=$subjectId'>
                EOD;
                $tmp = isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                $resSubjectsData[$howmanyA] .= <<<EOD
                $tmp
                EOD;
                $subjectName = isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                $subjectId = $index. '-' .$timeIndex; // 科目IDがない場合はデフォルトのIDを作る
                $subjectIDs[$howmanyA] = $subjectId; 
                $maxabsent = 0; //　時間割に設定していないマスは0を表示
                $resSubjectsData[$howmanyA] .= '</button>';
            }
            $howmanyA += 1;
        endforeach;
    endforeach;

    foreach ($days as $index => $day) :
        foreach ($times as $timeIndex) :
            $resSubjectsDetail[$howmanyB] .= <<<EOD
            <div class='overlay-absent-$howmanyB' id='overlay-absent'><div class='popup-absent' id='popup-absent'><p class='close-absent' id='close-absent'>&times;</p><div class='sm-w'><p class='name-subjects'>
            EOD;
            if ($subjectsByDay[$index][$timeIndex - 1]) {
                if ($subjectsByDay[$index][$timeIndex - 1] == 1) {
                    // 国語IVのid対策用
                    $row_no = $time_schdule->num_rows - $subjectsByDay[$index][$timeIndex - 1];
                } else {
                    $row_no = $time_schdule->num_rows - $subjectsByDay[$index][$timeIndex - 1] + 1;
                }
                $time_schdule->data_seek($row_no);
                $row = $time_schdule->fetch_assoc();
                $u_subjectId = $subjectIDs[$howmanyB];
                $subjectName = $row["科目名"];
                $subjectIF = $row["特殊欠席条件"];
                $subjectRate = $row["評価割合"];
                $resSubjectsDetail[$howmanyB] .= <<<EOD
                $subjectName</p><p class='teacher-subjects'></p>
                EOD;
                if (!empty($row["特殊欠席条件"])) {
                    $resSubjectsDetail[$howmanyB] .= <<<EOD
                    <p class = 'absent-condition'>$subjectIF</p>
                    EOD;
                } else {
                    $resSubjectsDetail[$howmanyB] .= <<<EOD
                    <p class = 'absent-condition'>特殊欠席条件はありません</p>
                    EOD;
                }
                $resSubjectsDetail[$howmanyB] .= <<<EOD
                <div class='scroll'><table class='rating-subjects'>$subjectRate</table></div><p class='absent-msg'>本当に欠席しますか？</p><button id='absenceButton_$howmanyB'  class='absenceButton_$u_subjectId absent-btn' data-subject-id='$u_subjectId'>欠席する</button>
                EOD;
            } else {
                $resSubjectsDetail[$howmanyB] .= <<<EOD
                <p class='name-subjects'>開きコマです</p><p class='name-subjects'>ゆっくりお休みください</p>
                EOD;
            }

            $resSubjectsDetail[$howmanyB] .= <<<EOD
                </div></div></div>
            EOD;
            $howmanyB += 1;
        endforeach;
    endforeach;

    $response = [$resSubjectsData, $resSubjectsDetail];
    $response = json_encode($response);
    echo $response;
}
