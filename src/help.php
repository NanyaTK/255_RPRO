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
        <a style="color: blue;text-decoration: none;" href="/main.php">留年プロテクターへ戻る</a>
        <div class="main-help">
            <h1>よくある質問</h1>
            <p>"v1.x.x"の表記がある回答について，修正次第追記します．</p>
            <a style="color: blue;text-decoration: none;" href="https://github.com/NanyaTK/255_RPRO/releases">詳しい更新情報はこちら(GitHub)</a>
            <ol style="margin-left: 50px;">
                <li>
                    <strong>Q: レイアウトが壊れていますが？</strong><br>
                    A: 古いキャッシュを参照している可能性があります．再起動ボタンを押下後にページを閉じて，再度アクセスすることで内部的に更新されます．<br>
                    追記：v1.4.1にて再起動ボタンを押下するだけでよくなりました．
                </li>
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
                    追記：どうしても使えない場合，再起動ボタンを押下してください．
                </li>
                <li>
                    <strong>Q: 欠席ボタンを間違って押した場合の修正手段ありますか？</strong><br>
                    A: v1.1.1現在存在しません．今後追加予定です．
                    追記：v1.4.0にて削除機能が追加されました．ご活用ください．
                </li>
                <li>
                    <strong>Q: 再読み込みしようとすると注意文がでますが？</strong><br>
                    A: 修正中です．
                    追記：v1.3.9にて処理方法が変更されたため，現在は表示されません．
                </li>
                <li>
                    <strong>Q: 前期で絞り込むとセレクタが空きコマのみになりますが？</strong><br>
                    A: v1.1.1現在，仕様です．リセットした後に後期で絞り込んでください．
                </li>
                <li>
                    <strong>Q: 現在のバージョンの確認方法は？</strong><br>
                    A: v1.1.0現在，直接的に確認する方法はありません．<br>
                    追記：v1.1.1にてヘッダーに表示するようになりました．
                </li>
                <li>
                    <strong>Q: ブラウザの戻るをするとページが存在しない？</strong><br>
                    A: 再読み込みしても解決しない場合，ページを閉じてアクセスしなおしてください．<br>
                    追記：v1.1.3にて修正されています．再起動ボタンを押下後にページを閉じて，アクセスしなおすことで内部的に更新されます．
                </li>
                <li>
                    <strong>Q: 科目詳細の表示がずれる</strong><br>
                    A: v1.3.3にて修正されています．再起動ボタンを押下後にページを閉じて，アクセスしなおすことで内部的に更新されます．
                </li>
                <li>
                    <strong>Q: 新規登録時に特定の科目を選択すると正しく登録されない</strong><br>
                    A: v1.3.7にて修正しました．
                </li>
                <li>
                    <strong>Q: ヘルプページからメインページへ戻るボタンが欲しい</strong><br>
                    A: v1.4.1にてリンクを追加しました．
                </li>
            </ol>
            <h1>留年プロテクターの使い方</h1>
            <ol style="margin-left: 50px;">
                <h3>新規登録編</h3>
                <li>新規登録ボタンを押下</li>
                <li>学科，学期を選択後，絞り込みを押下</li>
                <li>確定するを押下</li>
            </ol>
            <ol style="margin-left: 50px;">
                <h3>科目詳細編</h3>
                <li>時間割を押下</li>
                <li>科目詳細が表示される</li>
            </ol>
            <ol style="margin-left: 50px;">
                <h3>欠席編</h3>
                <li>時間割の科目を押下</li>
                <li>欠席するを押下</li>
                <li>カウントされる</li>
            </ol>
        </div>
    </div>
    <footer>
        &copy; 2024 留年プロテクタープロジェクト
    </footer>
</body>

</html>