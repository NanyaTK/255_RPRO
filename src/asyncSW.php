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
 * asyncSW.php
 * 
 * asyncSW.php is asynchronous processing file.
 */

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = json_decode(file_get_contents('php://input'), true);
    $data = explode(",", $data);
    if ($data[0] == "1"){
        array_shift($data);
        error_log("aaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
    }
    $mysqli = new mysqli("127.0.0.1", "rpro_u", "uhe6WTScplbJ", "rpro", 3306);
    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }
    $mysqli->query("use rpro");
    $result = $mysqli->prepare("
    SELECT
        `ID`                                        -- ID
        , `学科ID`                                  -- 学科ID
        , `年度`                                    -- 年度
        , `学期ID`                                  -- 学期ID
        , `時間割データ`                            -- 時間割データ
    FROM
        rpro.classformat 
    WHERE
        `学科ID` = ? and `学期ID` = ?
                        ");
    $result->bind_param("ii", $data[0], $data[1]);
    $result->execute();
    $result = $result->get_result();
    $row = $result->fetch_assoc();
    $mysqli->close();

    $output = array();
    $output = $row["時間割データ"];

    header('Content-Type: application/json');
    $response = $output;
    echo json_encode($response);
}
