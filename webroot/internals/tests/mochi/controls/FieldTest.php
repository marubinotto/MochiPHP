<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/Context.class.php');
require_once('mochi/Control.class.php');
require_once('mochi/controls/Field.class.php');
require_once('mochi/controls/Form.class.php');
require_once('mochi/utils/Messages.class.php');

class FieldTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		$this->object = new FieldImpl("field-name");
	}
	
	// id
	
	function test_setId() {
		$this->object->setId("field-id");
		$this->assertEquals("field-id", $this->object->getId());
	}
	
	function test_nameAsId() {
		$this->assertEquals("field-name", $this->object->getId());
	}
	
	function test_idWithForm() {
		$form = new Form("form-name");
		$form->addField($this->object);
		$this->assertEquals("form-name_field-name", $this->object->getId());
	}
	
	function test_setOmitId() {
		$this->object->setOmitId(TRUE);
		$this->assertNull($this->object->getId());
	}
	
	// value
	
	function test_rawValueIsNullInitially() {
		$this->assertNull($this->object->getRawValue());
	}
	
	function test_valueIsNullIfRawValueIsEmptyString() {
		$this->object->setRawValue("");
		$this->assertNull($this->object->getValue());
	}
	
	function test_valueIsRawValueByDefault() {
		$this->object->setRawValue("hello");
		$this->assertEquals("hello", $this->object->getValue());
	}
	
	function test_setValue() {
		$this->object->setValue("hello");
		
		$this->assertEquals("hello", $this->object->getValue());
		$this->assertEquals("hello", $this->object->getRawValue());
	}
	
	function test_setState() {
		$this->object->setState(new Context(array("field-name" => "hello")));
		$this->assertEquals("hello", $this->object->getRawValue());
	}
	
	function test_restoreState() {
		$this->object->restoreState(array("field-name" => "hello"));
		$this->assertEquals("hello", $this->object->getRawValue());
	}
	
	function test_restoreStateAsNull() {
		$this->object->setRawValue("hello");
		$this->assertEquals("hello", $this->object->getRawValue());
		
		$this->object->restoreState(array("hoge" => "huga"));
		
		$this->assertNull($this->object->getRawValue());
	}
	
	// validation
	
	function test_isValid() {
		$this->assertTrue($this->object->isValid());
	}
		
	function test_isNotValid() {
		$this->object->setError('field error');
		$this->assertFalse($this->object->isValid());
	}
	
	function test_setErrorMessageWithoutMessages() {
		$this->object->setErrorMessage("hello-world");
		$this->assertEquals(
			"hello-world {'field-name.display-name'}", 
			$this->object->getError());
	}
	
	function test_setErrorMessage() {
		Control::setMessages(new Messages(array(
			"field-name.display-name" => "Title",
			"error-required" => "You must enter a value for %s"
		)));
		$this->object->setErrorMessage("error-required");
		$this->assertEquals("You must enter a value for Title", $this->object->getError());
	}
		
	function test_setErrorMessageWithArg() {
		Control::setMessages(new Messages(array(
			"field-name.display-name" => "URL",
			"error-pattern" => '%1$s format must be "%2$s"'
		)));
		$this->object->setErrorMessage("error-pattern", array("(http|https)://.+"));
		$this->assertEquals(
			'URL format must be "(http|https)://.+"', 
			$this->object->getError());
	}
	
	// renderAsHidden
	
	function test_renderAsHidden() {
		$this->object->setRawValue("hello");
		$this->assertEquals(
			'<input type="hidden" name="field-name" value="hello" id="field-name"/>', 
			$this->object->renderAsHidden());
	}
}

class FieldImpl extends Field
{
	function __construct($name) {
		parent::__construct($name);
	}
	
	function render() {
		return "";
	}
	
	protected function validate() {
	}
}
?>
