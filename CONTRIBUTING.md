## 準備
1. git, php開発環境を準備
1. リポジトリを複製

## 開発の手順(VS Code,WSL)
1. ```develop```ブランチから開発ブランチを発行する．ブランチの命名規則は次節参照のこと．ブランチが最新であることを確認すること．
1. ブランチへ移動する．
1. 細やかにコミットをしながら開発する．コミットメッセージの規則は次節参照のこと．
1. 実装後，pushする．
1. GitHubでPRを作成する．
1. PRがマージされたらブランチを消し，手順1に戻り開発を続ける．<br>
```git branch -d ブランチの名前```

## 命名規則
### ブランチ
機能実装の場合：feat-#xx<br>
〇〇には実装する機能について簡潔に名づける．#xxはissue等を参照．<br>
ドキュメントの場合：doc-#xx<br>
リファクタの場合：refactor<br>
バグの修正の場合：fix-#xx<br>
issueの番号とする．ない場合は自分でissueをたてる．

### コミットメッセージ
ブランチの先頭の文字列: コミット内容とする<br>
e.g.<br>
feat: ユーザーデータの保存の実装

## 環境変数
### インストール
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```
### プロジェクトに追加
```composer require vlucas/phpdotenv```
.envファイルを追加
.gitignoreファイルを追加
.gitignoreファイルの中身↓↓
```
.env
/vendor/
```
.envファイルの中身↓↓
```
DB_HOST=""
DB_USER=""
DB_PASS=""
DB_NAME=""
DB_PORT=""
```