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
define("APPLICCATION_VERSION", "v1.5.0");

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
                    <li> <button id="delete-btn">データ削除</button></li>
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
                            <table id="table-signup" class="timetable-signup">
                                <tr>
                                    <th class="day-column">曜日</th>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                </tr>

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
                        <p>時間割 / 出欠データを削除します．</p>
                        <p>データを復旧することはできません．</p>
                        <button id="deletefinalize-btn">削除</button>
                    </div>
                </div>
            </div>
            <div class="jikanwari">
                <script>
                    console.log('[process: main] subjects finish');
                </script>
                <table class="timetable">
                    <tr>
                        <th class="day-column">曜日</th>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                    </tr>
                    <tr>
                        <td class="day-column">月</td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                    </tr>
                    <tr>
                        <td class="day-column">火</td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                    </tr>
                    <tr>
                        <td class="day-column">水</td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                    </tr>
                    <tr>
                        <td class="day-column">木</td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                    </tr>
                    <tr>
                        <td class="day-column">金</td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                        <td class="time-cell asyncCNN"></td>
                    </tr>
                </table>
            </div>
            <div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
                <div class="asyncCD"></div>
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