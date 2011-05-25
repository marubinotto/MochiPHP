<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/FrontController.class.php');

class FrontControllerStaticTest extends PHPUnit_Framework_TestCase
{
	function test_absolutePathToRedirectLocation() {
		$this->assertEquals(
			"/base/error.php", 
			FrontController::toRedirectLocation(
				"/error.php", 
				new Context(array(), array("SCRIPT_NAME" => "/base/front.php"))));
	}
	
	function test_relativePathToRedirectLocation() {
		$this->assertEquals(
			"error.php", 
			FrontController::toRedirectLocation(
				"error.php", 
				new Context(array(), array("SCRIPT_NAME" => "/base/front.php"))));
	}
		
	function test_fullUrlToRedirectLocation() {
		$this->assertEquals(
			"http://piggydb.net/", 
			FrontController::toRedirectLocation(
				"http://piggydb.net/", 
				new Context(array(), array("SCRIPT_NAME" => "/base/front.php"))));
	}
}

class PageResolverTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		$this->object = new PageResolver();
	}
	
	function test_getPageInfo() {
		$pageInfo = $this->object->getPageInfo(new MockContext("/path/to/resource"));
		$this->assertEquals("/path/to/resource.php", $pageInfo->filePath);
		$this->assertEquals("ResourcePage", $pageInfo->className);
		$this->assertEquals("path/to/resource", $pageInfo->templateName);
	}
	
	function test_twoWordsResource() {
		$pageInfo = $this->object->getPageInfo(new MockContext("/path/to/foo-bar"));
		$this->assertEquals("/path/to/foo-bar.php", $pageInfo->filePath);
		$this->assertEquals("FooBarPage", $pageInfo->className);
		$this->assertEquals("path/to/foo-bar", $pageInfo->templateName);
	}
	
	function test_returnDefaultIfPathIsDirectory() {
		$pageInfo = $this->object->getPageInfo(
			new MockContext("/path/to/", array("system.page.default" => "index")));
		$this->assertEquals("/path/to/index.php", $pageInfo->filePath);
		$this->assertEquals("IndexPage", $pageInfo->className);
		$this->assertEquals("path/to/index", $pageInfo->templateName);
	}
}
?>
