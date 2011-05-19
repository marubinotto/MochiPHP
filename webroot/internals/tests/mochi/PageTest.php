<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/Page.class.php');

class PageTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		$this->object = new PageImpl();
	}
	
	function test_getMessage() {
		$this->object->setMessages(new Messages(array("name" => "value")));
		$this->assertEquals("value", $this->object->getMessage("name"));
	}
}

class PageImpl extends Page
{
	
}
?>
