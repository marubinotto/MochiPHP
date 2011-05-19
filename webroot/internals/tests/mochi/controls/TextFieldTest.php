<?php
require_once('mochi/controls/TextField.class.php');

class TextFieldTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		Control::setMessages(NULL);
		$this->object = new TextField("field-name");
	}
	
	function test_name() {
		$this->assertEquals("field-name", $this->object->getName());
	}
	
	function test_noValidations() {
		$this->object->validate();
		$this->assertTrue($this->object->isValid());
	}
		
	function test_validate_required() {
		$this->object->setRequired(TRUE);
		
		$this->object->validate();
		
		$this->assertFalse($this->object->isValid());
		$this->assertEquals(
			"field-error-required {'field-name.display-name'}", 
			$this->object->getError());
	}
	
	function test_render() {
		$this->assertEquals(
			'<input type="text" name="field-name" id="field-name"/>', 
			$this->object->render());
	}
}
?>
