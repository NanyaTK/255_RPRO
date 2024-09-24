<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $data = explode(",", $data);
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
