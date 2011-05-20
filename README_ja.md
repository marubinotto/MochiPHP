MochiPHP Framework
==================

MochiPHPは、以下の仕組みをサポートする軽量なPHP用Webフレームワークです。

* ページ毎にクラスとテンプレートを書くページ指向
* Form部品はそれぞれクラスとしてコンポーネント化
* プロパティアクセサの自動生成をサポートした永続オブジェクト（ActiveRecord）

サポートするPHPのバージョンは、プロパティアクセサの自動生成を利用する場合は5.3.2以上、
利用しない場合（自前でgetter, setterを書く）、おそら5.1以上であれば動作するはずです
（5.1.6で動作確認済み）。

Getting Started
---------------

まずApacheが以下の条件を満たしていることを確認します。

* mod_rewrite が有効になっている事
* .htaccess が利用でき、/webroot/.htaccess にあるディレクティブの設定が許可されている事
   * 参考: [Apache チュートリアル: .htaccess ファイル](http://httpd.apache.org/docs/2.2/ja/howto/htaccess.html)

/webroot 以下のファイルを、ドキュメントルート以下の好きな場所にコピーします。

front.php をドキュメントルート直下に配置した場合、Apacheが起動していることを確認して、
以下のURLへアクセスします。

    http://localhost/hello
    
以下のようなメッセージが表示されれば準備完了です。

    Hello, world!

(※) Windows環境の場合は、/webroot/.htaccess を編集する必要があります。
詳しくはファイルの中身を参照して下さい。
