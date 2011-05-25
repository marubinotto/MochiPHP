<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/Context.class.php');

class ContextTest extends PHPUnit_Framework_TestCase
{
	function test_getParameter() {
		$object = new Context(array("message" => "hello"));
		$this->assertEquals("hello", $object->getParameter("message"));
		$this->assertNull($object->getParameter("no-such-name"));
	}

	function test_getServerVar() {
		$object = new Context(array(), array("SCRIPT_NAME" => "/test/front.php"));
		$this->assertEquals("/test/front.php", $object->getServerVar("SCRIPT_NAME"));
		$this->assertNull($object->getServerVar("no-such-name"));
	}

	function test_getBasePath_root() {
		$object = new Context(array(), array("SCRIPT_NAME" => "/front.php"));
		$this->assertEquals("", $object->getBasePath());
	}
	
	function test_getBasePath_subdirectory() {
		$object = new Context(array(), array("SCRIPT_NAME" => "/test/front.php"));
		$this->assertEquals("/test", $object->getBasePath());
	}

	function test_emptyResourcePath() {
		$object = new Context(array(), array(
			"SCRIPT_NAME" => "/test/front.php", 
			"REDIRECT_URL" => "/test"));		// without a slash
		$this->assertEquals("/", $object->getResourcePath()->getPath());
		
		$object = new Context(array(), array(
			"SCRIPT_NAME" => "/test/front.php", 
			"REDIRECT_URL" => "/test/"));	// with a slash
		$this->assertEquals("/", $object->getResourcePath()->getPath());
	}
	
	function test_resourcePath() {
		$object = new Context(array(), array(
			"SCRIPT_NAME" => "/test/front.php", 
			"REDIRECT_URL" => "/test/resource-path"));
		$this->assertEquals("/resource-path", $object->getResourcePath()->getPath());
	}
		
	function test_resourcePathWithQuery() {
		$object = new Context(array(), array(
			"SCRIPT_NAME" => "/test/front.php", 
			"REDIRECT_URL" => "/test/resource-path?name=value"));
		$this->assertEquals("/resource-path", $object->getResourcePath()->getPath());
	}

	function test_getAppRoot() {
		$object = new Context(array(), array(), "/path/to/app");
		$this->assertEquals("/path/to/app", $object->getAppRoot());
	}
}

class ResourcePathTest extends PHPUnit_Framework_TestCase
{
	function test_getPath() {
		$path = new ResourcePath("/path/to/resource");
		$this->assertEquals("/path/to/resource", $path->getPath());
		$this->assertEquals("/path/to/resource", $path->__toString());
	}
	
	function test_getNameAndDirectoryPath() {
		$path = new ResourcePath("/resource");
		$this->assertEquals("resource", $path->getName());
		$this->assertEquals("/", $path->getDirectoryPath());
		
		$path = new ResourcePath("/path/to/resource");
		$this->assertEquals("resource", $path->getName());
		$this->assertEquals("/path/to/", $path->getDirectoryPath());
		
		$path = new ResourcePath("/");
		$this->assertEquals("", $path->getName());
		$this->assertEquals("/", $path->getDirectoryPath());
		
		$path = new ResourcePath("/path/to/");
		$this->assertEquals("", $path->getName());
		$this->assertEquals("/path/to/", $path->getDirectoryPath());
	}
	
	function test_isDirectory() {
		$path = new ResourcePath("/");
		$this->assertTrue($path->isDirectory());
		
		$path = new ResourcePath("/path/to/");
		$this->assertTrue($path->isDirectory());
		
		$path = new ResourcePath("/resource");
		$this->assertFalse($path->isDirectory());
	}
	
	function test_withAnotherName() {
		$path = new ResourcePath("/");
		$this->assertEquals("/foo", $path->withAnotherName("foo")->getPath());
		
		$path = new ResourcePath("/path/to/resource");
		$this->assertEquals("/path/to/foo", $path->withAnotherName("foo")->getPath());
	}
	
	function test_concat() {
		$path = new ResourcePath("/path/to/resource");
		$this->assertEquals("/path/to/resource.txt", $path . ".txt");
	}
}
?>
