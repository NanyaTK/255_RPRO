<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>留年プロテクター</title>
    <link href="main.css" rel="stylesheet">
    <script>
        var screenWidth = window.screen.width;
        if (screenWidth > 1000) {} else {
            window.location = "main-sp.php"
        }
    </script>
</head>

<body>
    <div class="content">
        <header>
            <a class="header" href="/">留年プロテクター</a>
        </header>

        <div class="main">
            <div class="empty"></div>
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

        <footer>
            &copy; 2024 留年プロテクタープロジェクト
        </footer>
    </div>
</body>

</html>