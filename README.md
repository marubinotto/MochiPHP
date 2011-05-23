MochiPHP Framework
==================

MochiPHP is a lightweight framework for PHP which adopts component and 
page oriented design (inspired by [Click Framework](http://click.apache.org/)) 
with a simple database and object-persistence library.

* Page oriented design (write a class and template pair per a page)
* Form components which hide complex HTML markup and user interaction handling
* Persistent object with auto-generated property accessors
   * You can easily follow the [Uniform Access Principle](http://martinfowler.com/bliki/UniformAccessPrinciple.html) without adding verbose getters and setters.

MochiPHP can be run on PHP >= 5.3.2 if you use auto-generated property accessors 
(because it depends on [ReflectionMethod::setAccessible](http://jp.php.net/manual/en/reflectionmethod.setaccessible.php)), 
otherwise PHP >= 5.1 (tested on 5.1.6).

Getting Started
---------------

Preconditions:

* mod_rewrite is enabled
* .htaccess file is enabled and the directives in `/webroot/.htaccess` are allowed to be overridden
   * cf. [Apache Tutorial: .htaccess files](http://httpd.apache.org/docs/2.2/howto/htaccess.html)

Copy all the files under the `/webroot` folder to somewhere under your document root.

* If you use Unix/Linux or Mac OS X, make sure the web server can write to the directory `internals/app/templates_c`.
* If you use Windows, you need to modify the `/webroot/.htaccess` file.

If `front.php` is placed in the document root directory, bring up a web browser and go to URL:

    http://localhost/hello
    
If the browser displays the message as below, MochiPHP is running correctly:

    Hello, world!

Minimal Application
-------------------

The page `/hello` in the above example is implemented in two files:

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

* The page `/hello` is composed of `HelloPage` class defined in `app/pages/hello.php` file and [Smarty](http://www.smarty.net/) template defined in `app/templates/hello.tpl` file.
* The method `HelloPage::onRender` will be called just before rendering the template. In the above code, `addModel` method is used to define data which can be referred from the template.
* The template `hello.tpl` defines user interface using HTML and data `{$message}` defined in `HelloPage` class.
* Defining public properties in a page class allows to accept HTTP parameters. In the above code, the property `$name` is defined. If you access `/hello?name=marubinotto`, the browser will display `Hello, marubinotto!`.

Form Handling
-------------

The main part of MochiPHP is the class library which encapsulates HTML form and input fields
as reusable and extensible components. It allows you to concentrate on the most important task 
of user interface (layout, style, etc) without writing complex HTML markup and validation logic.

MochiPHP comes with a sample application (`/form`) which demonstrates form handling as below:

![Form Example](https://github.com/marubinotto/MochiPHP/raw/master/docs/form-example.png)

This application is composed of the two files as the `/hello` example:

* [internals/app/pages/form.php](https://github.com/marubinotto/MochiPHP/blob/master/webroot/internals/app/pages/form.php)
* [internals/app/templates/form.tpl](https://github.com/marubinotto/MochiPHP/blob/master/webroot/internals/app/templates/form.tpl)

The following is the step-by-step illustration of the above code.

First of all, you need to define form structure building `Form` object:

	$this->form = new Form('form');
	$this->form->addField(new TextArea('content',
	  array("cols" => 50, "rows" => 3, "required" => true)));

Create a form object and add input fields via `addField` method as above. 
You can customize the behavior or other attributes of a field via its constructor or methods. 
For example, the setting `"required" => true` enables input validation as below:

![Form Validation](https://github.com/marubinotto/MochiPHP/raw/master/docs/form-validation.png)

You can set an event handler to a form which is called when the form is submitted:

	$this->form->setListenerOnValidSubmission($this->listenVia('onSubmit'));

In this case, `onSubmit` method of the page (`FormPage`) is registered as an event handler.

Then, you need to register the form object to the page using `Page::addControl` 
to allow the page template to access it:

	$this->addControl($this->form);

In the template `form.tpl`, you define the layout of the form:

	{$form->startTag()|smarty:nodefaults}
	{$form->renderErrors()|smarty:nodefaults}
	{$form->fields.content->render()|smarty:nodefaults}
	<br/><input type="submit" value=" Send "/>
	{$form->endTag()|smarty:nodefaults}

You need to include stylesheet and JavaScript references 
in order to use the functionalities of the library:

	<link type="text/css" rel="stylesheet" href="{$basePath}/assets/mochi/control.css"/>
	<script type="text/javascript" src="{$basePath}/assets/mochi/control.js"></script>

And the event handler, `onSubmit` method:

	function onSubmit($source, Context $context) {
	  // Store the sent data
	  array_unshift($this->entries, $this->form->getValue('content'));
	  $context->getSession()->set('entries', $this->entries);

	  // Redirect After Post
	  $this->setRedirectToSelf($context);
	  return false;
	}

`onSubmit` method get the submitted value via `getValue` method on the form,
and store it to the session. After that, redirect to the same page
following the [Redirect After Post pattern](http://en.wikipedia.org/wiki/Post/Redirect/Get).
The return value `false` means no other event handlers are invoked in this request.

Object Persistence
------------------

It is fairly easy to store objects in database with MochiPHP. Here is an example of PersistentObject:

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

While you can use an existing database to store objects, 
defining a table DDL as a class constant `TABLE_DEF` allows
you to create a table using the MochiPHP tool or library from it. 
Table name is inferred from class name like: class `BlogPost` → table `blog_post` 
(Class constant `TABLE_NAME` can be used to specify the table name manually).

The naming convention for object-property/table-column mapping is:

	$p_column_name;

This property will be mapped to a column named `column_name`. 
The visibility of persistent properties has to be private or protected.

In addition to the mapping, properties that start with prefix `p_` can be 
accessed via auto-generated accessors like:

	$instance = new BlogPost();
	$instance->title = 'Hello';
	$this->assertEquals('Hello', $instance->title);

These accessors can be overridden by defining a getter and setter (ex. `getTitle`, `setTitle`).

You need to define a repository class for a persistent object in order to interact with database.
 
	class BlogPostRepository extends PersistentObjectRepository
	{
	  function __construct(Database $database) {
	    parent::__construct($database);
	  }
	  
	  function getObjectClassName() {
	    return "BlogPost";
	  }
	}

The following is a test program of [CRUD operations](http://en.wikipedia.org/wiki/Create,_read,_update_and_delete) using a repository. You don't need to write SQL in these operations.

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




