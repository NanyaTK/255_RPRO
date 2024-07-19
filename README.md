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
1. ctrl+@でUbuntuのシェル(コマンド実行するやつ)を開ける<br>その後，次のコマンドでリポジトリを最新にする．
1. ```git fetch```