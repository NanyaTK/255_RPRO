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
 * asyncCL.php
 * 
 * asyncCL.php is asynchronous processing file.
 */

require __DIR__ .  '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];
$dbName = $_ENV['DB_NAME'];
$dbPort = $_ENV['DB_PORT'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $data = json_decode(file_get_contents('php://input'), true);
    // $data = explode(",", $data);
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
    // 曜日と時間割の初期データ
    $days = ['月', '火', '水', '木', '金'];
    $times = ['1', '2', '3', '4'];
    foreach ($days as $day) :
        $response .= <<<EOD
<tr><td class='day-column'>$day</td>
EOD;
        foreach ($times as $timeIndex => $time) :
            $response .= <<<EOD
<td class="time-cell"><label class="select-subject">
EOD;
            $selectId = 'mys-' . $day . '-' . $timeIndex;
            $response .= <<<EOD
<select id='echo $selectId' class='subject-select'><option class="empty">空コマ</option>
EOD;
            for ($row_no = $time_schdule->num_rows - 1; $row_no >= 0; $row_no--) {
                $time_schdule->data_seek($row_no);
                $row = $time_schdule->fetch_assoc();
                $escA = $row["ID"];
                $escB = $row["学科ID"];
                $escC = $row["科目名"];
                $response .= <<<EOD
<option id ='cs-$escA' class='c-$escB'>$escC</option>
EOD;
            }
            $response .= <<<EOD
</select></label></td>
EOD;
        endforeach;
        $response .= <<<EOD
</tr>
EOD;
    endforeach;

    echo json_encode($response);
}
