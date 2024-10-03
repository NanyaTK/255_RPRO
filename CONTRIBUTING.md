## 準備
1. gitをインストールする(WSLおすすめ) <br> WSLにgitを入れるなら[この記事](https://qiita.com/tommy_g/items/771ac45b89b02e8a5d64)を参考にするとよい．WSL自体のインストールは[この記事](https://learn.microsoft.com/ja-jp/windows/wsl/install)
1. ```code .```コマンドでVS Code起動 <br><br>
![参考画像](/image/Image_001.png)
<br><br>
1. 左のアクティビティーバーのgitのタブからリポジトリの複製を選択<br><br>
![参考画像](/image/Image_002.png)
<br><br>
1. "https://github.com/NanyaTK/255_RPRO"を入れてEnter
1. 開発フォルダを置きたいところでもう一度Enter<br>
    そのままの場所でも全く問題ない．

## 開発の手順(VS Code,WSL前提)
1. ctrl+@でUbuntuのシェル(コマンド実行するやつ)を開ける<br>その後，developブランチにいることを確認し，developブランチを最新にする．
1. ```git pull```
1. ブランチを作成し，そのブランチへ移動する．命名規則は次節参照のこと．<br>
```git branch feat-#xx-〇〇```<br>
```git checkout feat-#xx-〇〇```
1. コードを書く．実装する機能ごとにステージング&コミットする．まとまってコミットするよりは細かすぎる方がまし．コミットメッセージの規則は次節参照のこと．
1. 実装がひと段落ついたらpushする．
1. GitHubでPRを作成する．
1. PRがマージされたらブランチを消し，手順1に戻り開発を続ける．<br>
```git branch -d ブランチの名前```

## 命名規則
### ブランチ
機能実装の場合：feat-#xx-〇〇<br>
〇〇には実装する機能について簡潔に名づける．#xxはissue等を参照．<br>
ドキュメントの場合：doc-#xx-〇〇<br>
リファクタの場合：refactor<br>
バグの修正の場合：fix-#xx-〇〇<br>
issueの番号とする．ない場合は自分でissueをたてる．
### コミットメッセージ
ブランチの先頭の文字列: コミット内容とする<br>
e.g.<br>
feat: ユーザーデータの保存の実装
