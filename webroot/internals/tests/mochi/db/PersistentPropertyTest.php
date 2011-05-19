<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/db/PersistentObject.class.php');

class ObjectWithPersistentFields extends PersistentObject
{
	protected $p_field1 = "hello";
	protected $p_field2 = "hello";
	protected $p_field3;
	protected $field4;
	
	protected $p_multiple_words;
	
	function getField2() { 
		return $this->p_field2 . " world";
	}
	
	function setField3($content) {
		$this->p_field3 = '"' . $content . '"'; 
	}
}

class PersistentPropertyTest extends PHPUnit_Framework_TestCase
{
	protected $object;
	
	function setUp() {
		$this->object = new ObjectWithPersistentFields();
	}

	function test_names() {
		$this->assertEquals(
			array("field1", "field2", "field3", "multipleWords"), $this->object->getPersistentPropertyNames());
	}

	function test_value() {
		$this->assertEquals("hello", $this->object->getPersistentPropertyValue("field1"));
		$this->assertEquals("hello", $this->object->getPersistentPropertyValue("field2"));
	}
	
	function test_setValue() {
		$result = $this->object->setPersistentPropertyValue("field1", "bye");
		$this->assertTrue($result);
		$this->assertEquals("bye", $this->object->getPersistentPropertyValue("field1"));
	}
	
	function test_fieldWithoutPrefix() {
		try {
			$this->object->getPersistentPropertyValue("field4");
			$this->fail();
		}
		catch (ReflectionException $e) {}
		
		$result = $this->object->setPersistentPropertyValue("field4", "hogehoge");
		$this->assertFalse($result);
	}
	
	function test_asProperty() {
		// direct access
		$this->assertEquals("hello", $this->object->field1);
		// via getter if exists
		$this->assertEquals("hello world", $this->object->field2);
	}
	
	function test_assignValue() {
		// direct
		$this->object->field1 = "hoge";
		$this->assertEquals("hoge", $this->object->getPersistentPropertyValue("field1"));
		
		// via setter if exists
		$this->object->field3 = "hoge";
		$this->assertEquals('"hoge"', $this->object->getPersistentPropertyValue("field3"));
	}
	
	function test_unexistingFieldViaProperty() {
		try {
			$this->object->unexisting;
			$this->fail();
		}
		catch (AccessorNotFoundException $e) {}
		
		try {
			$this->object->unexisting = "hogehoge";
			$this->fail();
		}
		catch (AccessorNotFoundException $e) {}
	}

	function test_toDatabaseRow() {
		$this->assertEquals(
			array(
				"id" => NULL,
				"field1" => "hello",
				"field2" => "hello world",
				"field3" => NULL,
				"multiple_words" => NULL), 
			$this->object->toDatabaseRow());
	}

	function test_toValues() {
		$this->assertEquals(
			array(
				"id" => NULL,
				"field1" => "hello",
				"field2" => "hello world",
				"field3" => NULL,
				"multipleWords" => NULL), 
			$this->object->toValues());
	}

	function test_bindDatabaseRow() {
		$this->object->bindDatabaseRow(array(
			"field1" => "foo",
			"field3" => "bar",
			"multiple_words" => "hoge",
			"unexisting" => "huga"
		));
		
		$this->assertEquals("foo", $this->object->getPersistentPropertyValue("field1"));
		$this->assertEquals("hello", $this->object->getPersistentPropertyValue("field2"));
		$this->assertEquals('"bar"', $this->object->getPersistentPropertyValue("field3"));
		$this->assertEquals("hoge", $this->object->getPersistentPropertyValue("multipleWords"));
	}

	function test_bindValues() {
		$this->object->bindValues(array(
			"field1" => "foo",
			"field3" => "bar",
			"multipleWords" => "hoge",
			"unexisting" => "huga"
		));
		
		$this->assertEquals("foo", $this->object->getPersistentPropertyValue("field1"));
		$this->assertEquals("hello", $this->object->getPersistentPropertyValue("field2"));
		$this->assertEquals('"bar"', $this->object->getPersistentPropertyValue("field3"));
		$this->assertEquals("hoge", $this->object->getPersistentPropertyValue("multipleWords"));
	}

	function test_generateAccessorsCode() {
		// $this->assertEquals("", $this->object->generateAccessorsCode());
	}
}

class SubObjectWithPersistentFields extends ObjectWithPersistentFields {}

class InheritedPersistentPropertyTest extends PersistentPropertyTest
{
	function setUp() {
		$this->object = new SubObjectWithPersistentFields();
	}
}
?>
