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

If front.php is placed in the document root directory, bring up a web browser and go to URL:

    http://localhost/hello
    
If the browser displays the message as below, MochiPHP is running correctly:

    Hello, world!

NOTE: If you use Windows, you need to modify the `/webroot/.htaccess` file.

