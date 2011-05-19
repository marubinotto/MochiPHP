<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/utils/Messages.class.php');

class MessagesTest extends PHPUnit_Framework_TestCase
{
	function test_get() {
		$object = new Messages(array("name" => "value"));
		$this->assertEquals("value", $object->get("name"));
	}
	
	function test_getWithArgs() {
		$object = new Messages(array("name" => 'You must enter a value for %s'));
		$this->assertEquals(
			"You must enter a value for Title", 
			$object->get("name", array("Title")));
	}
	
	function test_defaultMessage() {
		$object = new Messages();
		
		$this->assertEquals("name", $object->get("name"));
		$this->assertEquals("name {'arg'}", $object->get("name", array("arg")));
	}
}
?>
