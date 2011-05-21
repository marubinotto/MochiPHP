MochiPHP Framework
==================

MochiPHPは、以下の仕組みをサポートする軽量なPHP用Webフレームワークです。

* ページ毎にクラスとテンプレートを書くページ指向
* Form部品はそれぞれクラスとしてコンポーネント化
* プロパティアクセサの自動生成をサポートした永続オブジェクト（ActiveRecord）

サポートするPHPのバージョンは、プロパティアクセサの自動生成を利用する場合は5.3.2以上、
利用しない場合であれば（自前でgetter, setterを書く）、おそらく5.1以上であれば動作するはずです
（5.1.6で動作確認済み）。

他のメジャーなPHPフレームワークを使わずにわざわざMochiPHPを使う理由があるとすれば、以下のようなことが挙げられるかもしれません。

* 必要最小限で簡潔なコード
   * 他のPHPフレームワークのコードをたまに眺めたりしますが、おそらくMochiPHPほど簡潔なものは少ないのではないかと思います。全てのコードを眺めてもそれほど時間はかかりません（一つ一つのメソッドも平均数行程度）。もちろんその分機能は少ないと思いますが、ユーザーが把握できるコード量に抑えたまま最低限の機能を確保するというポリシーです。気に食わない部分はすぐに直せますし、拡張するのも容易です。
* [TODO] 他のフレームワークをきちんと調べてから比較すること。

Getting Started
---------------

まずApacheが以下の条件を満たしていることを確認します。

* mod_rewrite が有効になっている事
* .htaccess が利用でき、`/webroot/.htaccess` にあるディレクティブの設定が許可されている事
   * 参考: [Apache チュートリアル: .htaccess ファイル](http://httpd.apache.org/docs/2.2/ja/howto/htaccess.html)

`/webroot` 以下のファイルを、ドキュメントルート以下の好きな場所にコピーします。

Linux、Mac OS Xなどの環境では、`internals/app/templates_c`
ディレクトリがWebサーバーから読み書きできるようにパーミッションを設定して下さい。

`front.php` がドキュメントルート直下にある場合、以下のURLへアクセスします。

    http://localhost/hello
    
以下のようなメッセージが表示されれば準備完了です。

    Hello, world!

(※) Windows環境の場合は、`/webroot/.htaccess` を編集する必要があります。
詳しくはファイルの中身を参照して下さい。

最小構成のプログラム
-------------------

上記の例でアクセスしたページ`/hello`は以下の二つのファイルから構成されています。

/internals/app/pages/hello.php

	<?php
	require_once('mochi/Page.class.php');
	
	class HelloPage extends Page
	{
	  public $name;
	  
	  function onRender(Context $context) {
	    parent::onRender($context);
	    
	    $name = is_null($this->name) ? 'world' : $this->name;
	    $this->addModel('message', "Hello, {$name}!");
	  }
	}
	?>


/internals/app/templates/hello.tpl

	{$message}
	
ポイントは以下、

* `/hello`ページに対応するのは、`app/pages/hello.php`ファイルに定義された`HelloPage`クラスと、`app/templates/hello.tpl`ファイルに定義された[Smarty](http://www.smarty.net/)テンプレート。
* `HelloPage::onRender`はPageクラスに定義済みのメソッドで、テンプレートが出力される直前に呼び出される。ここでは、`addModel`というメソッドを使って、テンプレートに渡すデータを定義している（ここでは`message`という名前のデータを定義）。
* テンプレート（`hello.tpl`）では、`HelloPage`で定義されたデータを参照しながら、ページの見た目を定義する。
* Pageクラスにpublicのプロパティを定義すると、HTTPパラメータを受け取ることができる。上記の例では、`$name`というプロパティが定義されている。試しに、URLを `/hello?name=marubinotto` とすると、`Hello, marubinotto!` と表示される。


