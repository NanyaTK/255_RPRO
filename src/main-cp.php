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
define("APPLICCATION_VERSION", "v1.1.2");

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

<body>
    <div class="jikanwari">
        <?php
        // ここから時間割表示
        if (!$subjects) {
            // POSTデータが無い時の時間割データの初期値
            $subjects = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
        }
        foreach ($subjects as &$subject) {
            $subject = substr($subject, -2);
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
                                echo ($row["科目名"]);
                                $subjectName = $row["科目名"];
                                $subjectId = $row["科目ID"]; // 科目ごとのIDを使う
                            } else {
                                echo ('<button class ="open-popup-btn-' . $howmanyA . ' subject" data-subject-id=' . $subjectId . '>');
                                echo isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                                $subjectName = isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                                $subjectId = $index . '-' . $timeIndex; // 科目IDがない場合はデフォルトのIDを作る
                            }
                            ?>
                            </button>
                            <p>欠席回数 <span id="absenceCount_<?php echo $howmanyA; ?>">0</span> / 最大欠席回数</p>
                            <?php
                            $howmanyA += 1; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>