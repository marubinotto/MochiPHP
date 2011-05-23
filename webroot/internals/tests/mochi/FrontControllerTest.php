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

class PageFactoryStaticTest extends PHPUnit_Framework_TestCase
{
	// resourceNameToClassName
	
	function test_resourceNameToClassName() {
		$this->assertEquals(
			"LoginPage", PageFactory::resourceNameToClassName("login"));
		$this->assertEquals(
			"EditCustomerPage", PageFactory::resourceNameToClassName("edit-customer"));
	}
}
?>
