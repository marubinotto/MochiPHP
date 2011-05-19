<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/controls/Select.class.php');

class SelectTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		Control::setMessages(NULL);
		$this->object = new Select("field-name");
	}
	
	function test_render() {
		$this->object->add(1, "option1");
		// $this->assertEquals("", $this->object->render());
	}
}
?>
