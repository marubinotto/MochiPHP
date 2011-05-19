<?php
require_once('mochi/controls/Checkbox.class.php');

class CheckboxTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		Control::setMessages(NULL);
		$this->object = new Checkbox("field-name");
	}
	
	// isChecked
	
	function test_notCheckedByDefault() {
		$this->assertFalse($this->object->isChecked());
	}
	
	function test_isCheckedIfRawValueIsNotNull() {
		$this->object->setRawValue("on");
		$this->assertTrue($this->object->isChecked());
	}
	
	// setChecked
	
	function test_setChecked() {
		$this->object->setChecked(TRUE);
		
		$this->assertTrue($this->object->isChecked());
		$this->assertEquals("on", $this->object->getRawValue());
	}
	
	function test_setUnchecked() {
		$this->object->setChecked(FALSE);
		
		$this->assertFalse($this->object->isChecked());
		$this->assertNull($this->object->getRawValue());
	}
	
	// setValue
	
	function test_setValue() {
		$this->object->setValue(TRUE);
		$this->assertEquals("on", $this->object->getRawValue());
		
		$this->object->setValue(FALSE);
		$this->assertNull($this->object->getRawValue());
		
		$this->object->setValue("hogehoge");
		$this->assertEquals("hogehoge", $this->object->getRawValue());
		
		$this->object->setValue(NULL);
		$this->assertNull($this->object->getRawValue());
	}
	
	// getValue
	
	function test_valueIsFalseIfRawValueIsNull() {
		$this->assertNotNull($this->object->getValue());
		$this->assertFalse($this->object->getValue());
	}
	
	function test_valueIsTrueIfRawValueIsDefaultValue() {
		$this->object->setRawValue("on");
		
		$this->assertNotNull($this->object->getValue());
		$this->assertTrue($this->object->getValue());
	}
	
	function test_valueIsRawValueIfNotDefaultValue() {
		$this->object->setRawValue("hogehoge");
		$this->assertEquals("hogehoge", $this->object->getValue());
	}
}
?>
