MochiPHP Framework
==================

MochiPHPは、以下の仕組みをサポートする軽量なPHP用Webフレームワークです。

* ページ毎にクラスとテンプレートを書くページ指向
* Form部品はそれぞれクラスとしてコンポーネント化
* プロパティアクセサの自動生成をサポートした永続オブジェクト（ActiveRecord）

サポートするPHPのバージョンは、プロパティアクセサの自動生成を利用する場合は5.3.2以上、
利用しない場合であれば（自前でgetter, setterを書く）、おそらく5.1以上であれば動作するはずです
（5.1.6で動作確認済み）。

他のメジャーなPHPフレームワークを使わずにわざわざMochiPHPを使う理由があるとすれば、
以下のようなことが挙げられるかもしれません。

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

* Linux、Mac OS Xなどの環境では、`internals/app/templates_c` ディレクトリがWebサーバーから読み書きできるようにパーミッションを設定して下さい。
* Windows環境の場合は、`/webroot/.htaccess` を編集する必要があります。詳しくはファイルの中身を参照して下さい。

`front.php` がドキュメントルート直下にある場合、以下のURLへアクセスします。

    http://localhost/hello
    
以下のようなメッセージが表示されれば準備完了です。

    Hello, world!

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

Form処理
--------

MochiPHPでは、テキストフィールドなどのForm部品がクラスライブラリとして提供されています。
これらのクラスを利用することによって、Formにまつわる面倒な詳細
（複雑なHTMLやバリデーション処理など）を書かずに済みます。

以下のようなTwitterっぽいアプリケーションがサンプルプログラムに含まれています（`/form`）。

![Form Example](https://github.com/marubinotto/MochiPHP/raw/master/docs/form-example.png)

このアプリケーションは`/hello`と同様、以下の２つのファイルから構成されています。

* [internals/app/pages/form.php](https://github.com/marubinotto/MochiPHP/blob/master/webroot/internals/app/pages/form.php)
* [internals/app/templates/form.tpl](https://github.com/marubinotto/MochiPHP/blob/master/webroot/internals/app/templates/form.tpl)

以下、順を追って説明します。

まずは、`Form`クラスを利用してFormの構成を定義します。

	$this->form = new Form('form');
	$this->form->addField(new TextArea('content',
	  array("cols" => 50, "rows" => 3, "required" => true)));

Formに対して`TextArea`のような入力フィールドを追加（`addField`）していきます。
それぞれの入力フィールドの設定についてもここで行います。例えば、`"required" => true` 
という設定はこの項目について入力が必須であることを表しています。
この設定により、以下のようなバリデーション処理が自動で行われます。

![Form Validation](https://github.com/marubinotto/MochiPHP/raw/master/docs/form-validation.png)

通常、Formの定義は `Page::onPrepare` というメソッドで行います。
onPrepareは、ページで行われる主要な処理（パラメータの設定やイベントハンドラなど）
の前に呼び出されます。

以下では、このFormについて、正しいデータとともにSubmitが行われた場合に呼び出される
イベントハンドラの設定を行っています。以下の設定により、ページクラスの`onSubmit`という
メソッドが呼び出されます。

	$this->form->setListenerOnValidSubmission($this->listenVia('onSubmit'));
	
このFormをページに登録します。これによって、テンプレートからこのFormを参照できるようになります。

	$this->addControl($this->form);

テンプレート側では、以下のようにFormとその入力フィールドの配置を記述します。

	{$form->startTag()|smarty:nodefaults}
	{$form->renderErrors()|smarty:nodefaults}
	{$form->fields.content->render()|smarty:nodefaults}
	<br/><input type="submit" value=" Send "/>
	{$form->endTag()|smarty:nodefaults}
	
Formライブラリの機能を利用するため、テンプレートでは以下のように
スタイルシートやJavaScriptのファイルを指定しておく必要があります。

	<link type="text/css" rel="stylesheet" href="{$basePath}/assets/mochi/control.css"/>
	<script type="text/javascript" src="{$basePath}/assets/mochi/control.js"></script>

以下は、FormがSubmitされた際に呼び出されるメソッドです。

	function onSubmit($source, Context $context) {
	  // Store the sent data
	  array_unshift($this->entries, $this->form->getValue('content'));
	  $context->getSession()->set('entries', $this->entries);

	  // Redirect After Post
	  $this->setRedirectToSelf($context);
	  return false;
	}

Formによって送信されたパラメータを`getValue`で取得し、
それを配列に追加してからセッションに登録しています。
ここは通常、データベースなどを利用したトランザクションを行う場所です。

以上の処理が終わった後は、Redirect After Postパターンに従い同じページにリダイレクトさせます。
戻り値が `false` になっているのは、リダイレクトするので、
ここでは以降の描画処理などをスキップする、という指定です。

オブジェクトの永続化
-------------------

MochiPHPでオブジェクトをデータベースに保存するのはとても簡単です。
以下は永続化オブジェクトの定義例です。

	class BlogPost extends PersistentObject
	{
	  const TABLE_DEF = "
	    create table %s (
	      id integer unsigned not null auto_increment,
	    
	      title varchar(255) not null,
	      content text,
	      register_datetime datetime not null,
	      update_datetime datetime not null,
	      
	      primary key(id)
	    ) TYPE = InnoDB;
	    ";
	
	  protected $p_title;
	  protected $p_content;
	  protected $p_register_datetime;
	  protected $p_update_datetime;
	}

まず、オブジェクトを保存するテーブルについては、
既に構築済みのデータベースを利用する事もできますし、
上の例のようにクラスに `TABLE_DEF` としてCREATE文を定義しておけば、
専用のツールやライブラリを利用してテーブルの作成を行う事ができます。
テーブル名はクラス名を元にして決定されます。
`BlogPost` の場合、テーブル名は `blog_post` となります
（クラス定数 `TABLE_NAME` を利用すれば、テーブル名を自分で指定可能）。

注意すべきは、オブジェクトのプロパティの命名規則だけです。

	$p_column_name;

という名前のプロパティがある場合、
このプロパティは `column_name` という名前のカラムにマッピングされます。
これらのプロパティはprivateかprotectedにする必要があります。

さらに、`p_`で始まる名前のプロパティにはアクセサが自動的に提供されます。
上の`BlogPost`の例だと、

	$instance = new BlogPost();
	$instance->title = 'Hello';
	$this->assertEquals('Hello', $instance->title);
	
のような形でプロパティにアクセスできます。
このアクセサは、`getTitle`, `setTitle` 
というメソッドを定義する事で読み書きの処理をオーバーライドすることができます。

`BlogPost`のような永続オブジェクトには、
対応するリポジトリクラスを定義するのがMochiPHPの流儀です。

	class BlogPostRepository extends PersistentObjectRepository
	{
	  function __construct(Database $database) {
	    parent::__construct($database);
	  }
	  
	  function getObjectClassName() {
	    return "BlogPost";
	  }
	}
	
リポジトリは、オブジェクトをデータベースから取り出す際の窓口となるものです。
以下はリポジトリを使ってデータベースの[CRUDオペレーション](http://ja.wikipedia.org/wiki/CRUD)を行うテストプログラムです。簡単なCRUDであればSQLを書く必要はありません。

	// Create
	$instance = $this->repository->newInstance();
	$instance->title = 'MochiPHP';
	$instance->content = 'MochiPHP is a lightweight framework for PHP.';
	$now = $instance->formatTimestamp();
	$instance->registerDatetime = $now;
	$instance->updateDatetime = $now;
	$instance->save();
	$id = $instance->id;
	
	$this->assertEquals(1, $this->repository->count());
	
	// Read
	$instance = $this->repository->findById($id);
	$this->assertEquals('MochiPHP', $instance->title);
	$this->assertEquals('MochiPHP is a lightweight framework for PHP.', $instance->content);
	
	// Update
	$instance->title = "What is MochiPHP?";
	$instance->updateDatetime = $instance->formatTimestamp();
	$instance->save();
	
	$instance = $this->repository->findById($id);
	$this->assertEquals('What is MochiPHP?', $instance->title);
	
	// Delete
	$this->repository->deleteById($id);
	$this->assertEquals(0, $this->repository->count());

https://github.com/marubinotto/MochiPHP/blob/master/webroot/internals/app/tests/models/BlogPostTest.php

