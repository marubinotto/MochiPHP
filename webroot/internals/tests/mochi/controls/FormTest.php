<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/Context.class.php');
require_once('mochi/controls/Form.class.php');
require_once('mochi/controls/Field.class.php');

class FormTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		$this->object = new Form("form-name");
	}
	
	function test_id() {
		$this->object->setId("form-id");
		$this->assertEquals("form-id", $this->object->getId());
	}
	
	function test_nameAsDefaultId() {
		$this->assertEquals("form-name", $this->object->getId());
	}
	
	function test_hasNotSubmittedYet() {
		$this->assertFalse($this->object->isSubmitted());
	}
	
	function test_isSubmitted() {
		$this->object->setState(new Context(array("_formName" => "form-name")));
		$this->assertTrue($this->object->isSubmitted());
	}

	function test_isValid() {
		$this->assertTrue($this->object->isValid());
	}
		
	function test_isNotValid() {
		$this->object->setError('form error');
		$this->assertFalse($this->object->isValid());
		
		// $this->assertEquals("", $this->object->renderErrors());
	}
	
	// dispatchEvent
	
	function test_dispatchEvent() {
		$context = new Context(array("_formName" => "form-name"));
		$listener = new MockEventListener();
		$this->object->setListenerOnValidSubmission($listener);
		$this->object->setState($context);
		
		$result = $this->object->dispatchEvent($context);
		
		$this->assertFalse($result);
		$this->assertSame($this->object, $listener->source);
		$this->assertSame($context, $listener->context);
	}
	
	function test_dispatchEventWhenNotSubmitted() {
		$listener = new MockEventListener();
		$this->object->setListenerOnValidSubmission($listener);
		
		$result = $this->object->dispatchEvent(new Context());
		
		$this->assertTrue($result);
		$this->assertNull($listener->source);
		$this->assertNull($listener->context);
	}
	
	function test_dispatchEventWithoutListener() {
		$context = new Context(array("_formName" => "form-name"));
		$this->object->setState($context);
		
		$result = $this->object->dispatchEvent($context);
		
		$this->assertTrue($result);
	}
}

class MockEventListener
{
	public $source;
	public $context;
	
	function invoke($source, $context) {
		$this->source = $source;
		$this->context = $context;
		return FALSE;
	}
}

class OneFieldFormTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $field;
	
	function setUp() {
		$this->object = new Form("form-name");
		$this->field = new MockField('field-name');
		$this->object->addField($this->field);
	}
	
	function test_setFormToField() {
		$this->assertSame($this->object, $this->field->getForm());
	}
	
	function test_setState() {
		$this->object->setState(new Context(array(
			"_formName" => "form-name", 
			"field-name" => "hello")));
			
		$this->assertEquals("hello", $this->field->getRawValue());
	}
	
	function test_getValue() {
		$this->field->setRawValue("hogehoge");
		$this->assertEquals("hogehoge", $this->object->getValue("field-name"));
	}
	
	function test_getValueOfUnexistingField() {
		$this->setExpectedException('Exception');
		$this->object->getValue("unexisting");
	}
	
	function test_getValues() {
		$this->field->setRawValue("hogehoge");
		$this->assertEquals(
			array("field-name" => "hogehoge"), 
			$this->object->getValues());
	}
	
	function test_setValue() {
		$this->object->setValue("field-name", "hello");
		$this->assertEquals("hello", $this->object->getValue("field-name"));
	}
	
	function test_setValues() {
		$this->object->setValues(array("field-name" => "hello", "unexisting" => "foo"));
		$this->assertEquals("hello", $this->object->getValue("field-name"));
	}
	
	function test_isValid() {
		$this->assertTrue($this->object->isValid());
	}
	
	function test_zeroErrorFields() {
		$this->assertEquals(0, count($this->object->getErrorFields()));
	}
	
	function test_fieldIsNotValid() {
		$this->field->setError('field error');
		$this->assertFalse($this->object->isValid());
	}
		
	function test_oneErrorFields() {
		$this->field->setError('field error');
		
		$errorFields = $this->object->getErrorFields();
		$this->assertEquals(1, count($errorFields));
		$this->assertEquals("field-name", $errorFields[0]->getName());
		
		// $this->assertEquals("", var_export($this->object->toValueTree(), TRUE));
		// $this->assertEquals("", $this->object->renderErrors());
	}
}

class MockField extends Field
{
	public $validated = FALSE;
	
	function __construct($name) {
		parent::__construct($name);
	}
	
	function render() {
		return "";
	}
	
	function validate() {
		$this->validated = TRUE;
	}
}
?>
