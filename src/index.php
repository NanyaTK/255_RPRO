<?php
// POSTリクエスト処理を分ける
/*function handlePostRequest() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        // データをファイルに保存

        // データを返す
        return $data;
    }

    file_put_contents('subjects.json', 'aaaa',FILE_APPEND);
    // POSTデータがない場合はデフォルトのデータを返す
    return json_decode('["","","","","","","","","","","","","","","","","","","",""]', true);
}

// このスクリプトが直接リクエストされた場合にのみPOSTリクエストを処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = handlePostRequest();
    echo json_encode(['status' => 'success', 'message' => 'Data received', 'updatedSubjects' => $data]);
    exit();
}*/