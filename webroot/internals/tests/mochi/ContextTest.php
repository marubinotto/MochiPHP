<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/Context.class.php');

class ContextTest extends PHPUnit_Framework_TestCase
{
	// getParameter
	
	function test_getParameter() {
		$object = new Context(array("message" => "hello"));
		$this->assertEquals("hello", $object->getParameter("message"));
	}
	
	function test_getUnexistingParameter() {
		$object = new Context();
		$this->assertNull($object->getParameter("no-such-name"));
	}
	
	//getServerVar
	
	function test_getServerVar() {
		$object = new Context(array(), array("SCRIPT_NAME" => "/test/front.php"));
		$this->assertEquals("/test/front.php", $object->getServerVar("SCRIPT_NAME"));
	}
	
	function test_getUnexistingServerVar() {
		$object = new Context();
		$this->assertNull($object->getServerVar("no-such-name"));
	}
	
	// getBasePath
	
	function test_getBasePath_root() {
		$object = new Context(array(), array("SCRIPT_NAME" => "/front.php"));
		$this->assertEquals("", $object->getBasePath());
	}
	
	function test_getBasePath_subdirectory() {
		$object = new Context(array(), array("SCRIPT_NAME" => "/test/front.php"));
		$this->assertEquals("/test", $object->getBasePath());
	}
	
	// getResourcePath
	
	function test_emptyResourcePath() {
		$object = new Context(array(), array(
			"SCRIPT_NAME" => "/test/front.php", 
			"REDIRECT_URL" => "/test"));		// without a slash
		$this->assertNotNull($object->getResourcePath());
		$this->assertEquals("/", $object->getResourcePath());
		
		$object = new Context(array(), array(
			"SCRIPT_NAME" => "/test/front.php", 
			"REDIRECT_URL" => "/test/"));	// with a slash
		$this->assertNotNull($object->getResourcePath());
		$this->assertEquals("/", $object->getResourcePath());
	}
	
	function test_resourcePath() {
		$object = new Context(array(), array(
			"SCRIPT_NAME" => "/test/front.php", 
			"REDIRECT_URL" => "/test/resource-path"));
		$this->assertEquals("/resource-path", $object->getResourcePath());
	}
		
	function test_resourcePathWithQuery() {
		$object = new Context(array(), array(
			"SCRIPT_NAME" => "/test/front.php", 
			"REDIRECT_URL" => "/test/resource-path?name=value"));
		$this->assertEquals("/resource-path", $object->getResourcePath());
	}
	
	// getResourceNameFromPath
	
	function test_getResourceNameFromPath() {
		$this->assertEquals(
			"login", 
			Context::getResourceNameFromPath("/login"));
		$this->assertEquals(
			"edit-customer", 
			Context::getResourceNameFromPath("/path/to/edit-customer"));
		$this->assertEquals("", Context::getResourceNameFromPath("/"));
		$this->assertEquals("", Context::getResourceNameFromPath("/path/to/"));
	}
	
	// getAppRoot
	
	function test_getAppRoot() {
		$object = new Context(array(), array(), "/path/to/app");
		$this->assertEquals("/path/to/app", $object->getAppRoot());
	}
}
?>
