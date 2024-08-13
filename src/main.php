<!DOCTYPE html>
<html lang="ja">

<?php
$mysqli = new mysqli("127.0.0.1","rpro_u","uhe6WTScplbJ","rpro.3306");
$result = $mysqli->query("SELECT 'choice to please everybody.' AS _msg FROM DUAL");
$row = $result->fetch_assoc();
echo $row['_msg'];
?>

<head>
    <meta charset="UTF-8" />
    <title>留年プロテクター</title>
    <link href="main.css" rel="stylesheet">
</head>

<body>
    <div class="content">
        <header>
            <a class="header" href="/">留年プロテクター</a>
        </header>

        <div class="main">
            <div class="empty"></div>
            <button id="signup-btn">新規登録</button>
            <!-- ここから新規登録画面 -->
            <div id="popup-wrapper">
                <div id="popup-inside">
                    <div id="close">x</div>
                    <div id="message">
                        <h2>新規登録</h2>
                        <p>時間割を設定してください</p>

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
                                                        <option>空コマ</option>
                                                        <option id="A">A</option>
                                                        <option id="B">B</option>
                                                        <option id="C">C</option>
                                                    </select>
                                                </label>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <button id="finalize-btn" onclick="getAllSelectedOptionIds()">確定する</button>
                        <p id="result"></p>

                        <p id="result"></p>
                        <script>
                            function getAllSelectedOptionIds() {
                                // .subject-selectクラスを持つ全てのselect要素を取得
                                const selectElements = document.querySelectorAll('.subject-select');
                                const selectedOptionIds = [];

                                // 各select要素をループして選択されたoptionのidを取得
                                selectElements.forEach(selectElement => {
                                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                                    const selectedOptionId = selectedOption.id;
                                    selectedOptionIds.push(selectedOptionId); // 配列に追加
                                });

                                // 結果を表示
                                document.getElementById("result").innerText = "Selected Option IDs: " + selectedOptionIds.join(', ');
                            }
                        </script>
                    </div>
                </div>
            </div>
            <!-- ここまで新規登録画面 -->
            <!-- ここから時間割表示　 -->
            <div class="jikanwari">
                <?php
                // 曜日と時間割の初期データ
                $days = ['月', '火', '水', '木', '金'];
                $times = ['1', '2', '3', '4'];
                ?>
                <table class="timetable">
                    <tr>
                        <th class="day-column">曜日</th>
                        <?php foreach ($times as $time) : ?>
                            <th><?php echo $time; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <?php foreach ($days as $day) : ?>
                        <tr>
                            <td class="day-column"><?php echo $day; ?></td>
                            <?php foreach ($times as $time) : ?>
                                <td class="time-cell">
                                    <!-- ここに科目を設定 -->
                                    <!-- 例: Math, Science, History -->
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <!-- ここまで時間割表示　 -->
        <footer>
            &copy; 2024 留年プロテクタープロジェクト
        </footer>
    </div>

    <script>
        const signUpBtn = document.getElementById('signup-btn');
        const popupWrapper = document.getElementById('popup-wrapper');
        const close = document.getElementById('close');

        // ボタンをクリックしたときにポップアップを表示させる
        signUpBtn.addEventListener('click', () => {
            popupWrapper.style.display = "block";
        });

        // ポップアップの外側又は「x」のマークをクリックしたときポップアップを閉じる
        popupWrapper.addEventListener('click', e => {
            if (e.target.id === popupWrapper.id || e.target.id === close.id) {
                popupWrapper.style.display = 'none';
            }
        });
    </script>

</body>

</html>