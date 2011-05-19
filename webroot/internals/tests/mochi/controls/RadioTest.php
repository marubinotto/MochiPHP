<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/controls/Radio.class.php');
require_once('mochi/Context.class.php');

class RadioTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		Control::setMessages(NULL);
		$this->object = new Radio("1", "display-name");
		$this->object->setName("field-name");
	}
	
	function test_getName() {
		$this->assertEquals("field-name", $this->object->getName());
	}
	
	function test_getId() {
		$this->assertEquals("field-name_1", $this->object->getId());
	}
	
	function test_setId() {
		$this->object->setId("specified-id");
		$this->assertEquals("specified-id", $this->object->getId());
	}
	
	function test_isNotCheckedByDefault() {
		$this->assertFalse($this->object->isChecked());
	}
	
	function test_setState() {
		$this->object->setState(new Context(array("field-name" => "2")));
		$this->assertFalse($this->object->isChecked());
		
		$this->object->setState(new Context(array("field-name" => "1")));
		$this->assertTrue($this->object->isChecked());
	}
	
	function test_setCheckedByValue() {
		$this->object->setCheckedByValue('2');
		$this->assertFalse($this->object->isChecked());
		
		$this->object->setCheckedByValue('1');
		$this->assertTrue($this->object->isChecked());
	}
}

class RadioGroupTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $radio1;
	private $radio2;
	
	function setUp() {
		Control::setMessages(NULL);
		
		$this->object = new RadioGroup("field-name");
		
		$this->radio1 = new Radio("1", "radio1");
		$this->radio2 = new Radio("2", "radio2");
		
		$this->object->add($this->radio1);
		$this->object->add($this->radio2);
	}
	
	function test_getName() {
		$this->assertEquals("field-name", $this->object->getName());
		$this->assertEquals("field-name", $this->radio1->getName());
		$this->assertEquals("field-name", $this->radio2->getName());
	}
	
	function test_radiosAreNotCheckedByDefault() {
		$this->assertFalse($this->radio1->isChecked());
		$this->assertFalse($this->radio2->isChecked());
	}
	
	function test_setState() {
		$this->object->setState(new Context(array("field-name" => "1")));
		
		$this->assertTrue($this->radio1->isChecked());
		$this->assertFalse($this->radio2->isChecked());
	}
	
	function test_select() {
		$this->object->select("2");
		
		$this->assertFalse($this->radio1->isChecked());
		$this->assertTrue($this->radio2->isChecked());
	}
	
	function test_getSelectedDisplayName() {
		$this->object->select("2");
		$this->assertEquals("radio2", $this->object->getSelectedDisplayName());
	}
}
?>
