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

// POSTされたデータを取得
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $data = explode(",", $data);
    $output = <<<HTML
    <?php foreach ($times as $timeIndex) : ?>
        <td class="time-cell">
            <?php
            if ($subjectsByDay[$index][$timeIndex - 1]) {
                if ($subjectsByDay[$index][$timeIndex - 1] == 1) {
                    $row_no = $time_schdule->num_rows - $subjectsByDay[$index][$timeIndex - 1];
                } else {
                    $row_no = $time_schdule->num_rows - $subjectsByDay[$index][$timeIndex - 1] + 1;
                }
                $time_schdule->data_seek($row_no);
                $row = $time_schdule->fetch_assoc();
                $subjectName = {$row["科目名"]};
                $subjectId = {$row["科目ID"]}; 
                echo ('<button id="absenceButton_' . $subjectId . '" class ="open-popup-btn-' . $howmanyA . ' subject" data-subject-id=' . $subjectId . '>');
                echo ($row{["科目名"]});
                $subjectName = $row{["科目名"]};
                $subjectId = $row{["科目ID"]};
            } else {
                echo ('<button class ="open-popup-btn-' . $howmanyA . ' subject" data-subject-id=' . $subjectId . '>');
                echo isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                $subjectName = isset($subjectsByDay[$index][$timeIndex - 1]) ? $subjectsByDay[$index][$timeIndex - 1] : '';
                $subjectId = $index . '-' . $timeIndex;
            }
            ?>
            </button>
            <p>欠席回数 <span id="absenceCount_<?php echo $howmanyA; ?>">0</span> / 最大欠席回数</p>
            <?php
            $howmanyA += 1; ?>
        </td>
    <?php endforeach; ?>
    HTML;
    $response = $output;
    echo json_encode($response);
}