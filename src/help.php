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
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>留年プロテクター ヘルプページ</title>
    <link rel="icon" href="/icon-images/favicon.ico">
    <link rel="stylesheet" href="main.css" />
</head>

<body>
    <header>
        <div class="flex-byForce">
            <a class="header" href="/help.php">留年プロテクター よくある質問・ヘルプ</a>
        </div>
    </header>
    <div class="content">
        <div class="main-help">
            <h1>よくある質問</h1>
            <p>"v1.x.x"の表記がある回答について，修正次第追記します．</p>
            <ol>
                <li>
                    <strong>Q: 時間割の曜日と時間縦横逆の方がうれしい</strong><br>
                    A: スマートフォンの横幅との兼ね合いで今のレイアウトになっていますが，見やすさを確保しながら変更できるか模索します
                </li>
                <li>
                    <strong>Q: 新規登録画面の授業科目の一覧2周してない？</strong><br>
                    A: 絞り込んでないと全学科全学年の全データが出力されます．2周しているように見えるのは電気コースと情報コースの重複分です．絞り込んで使用ください．
                </li>
                <li>
                    <strong>Q: 絞り込みが2回目以降使えなくなりました</strong><br>
                    A: 絞り込みの仕様上，一度絞り込んだ後はリセットして，その後もう一度絞り込んでください．
                </li>
                <li>
                    <strong>Q: 欠席ボタンを間違って押した場合の修正手段ありますか？</strong><br>
                    A: v1.1.1現在存在しません．今後追加予定です．
                </li>
                <li>
                    <strong>Q: 再読み込みしようとすると注意文がでますが？</strong><br>
                    A: 修正中です．
                </li>
                <li>
                    <strong>Q: 前期で絞り込むとセレクタが空きコマのみになりますが？</strong><br>
                    A: v1.1.1現在，仕様です．リセットした後に後期で絞り込んでください．
                </li>
                <li>
                    <strong>Q: 現在のバージョンの確認方法は？</strong><br>
                    A: v1.1.0現在，直接的に確認する方法はありません．
                </li>
            </ol>

        </div>
    </div>
    <footer>
        &copy; 2024 留年プロテクタープロジェクト
    </footer>
</body>

</html>