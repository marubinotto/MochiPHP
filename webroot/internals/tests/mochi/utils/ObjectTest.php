<?php
require_once('mochi/utils/Object.class.php');

// ObjectTest

class ObjectImpl1 extends Object
{
	public $public = "foo";
	protected $protected = "bar";
	private $private = "baz";
	
	function getProtected() {
		return $this->protected;
	}
	
	function getPrivate() {
		return $this->private;
	}
}

class ObjectTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		$this->object = new ObjectImpl1();
	}
	
	function test_getClassName() {
		$this->assertEquals("ObjectImpl1", $this->object->getClassName());
	}
	
	function test_supportsPrivateAccess() {
		$this->assertTrue($this->object->supportsPrivateAccess());
	}

	function test_getPropertyValues() {
		$values = $this->object->getPropertyValues();
		
		$this->assertEquals(
			array("public" => "foo", "protected" => "bar", "private" => "baz"), 
			$values);
	}
	
	function test_setPublicPropertyValue() {
		$result = $this->object->setPublicPropertyValue("public", "hello");
		
		$this->assertTrue($result);
		$this->assertEquals("hello", $this->object->public);
	}
	
	function test_setPublicPropertyValueButProtected() {
		$result = $this->object->setPublicPropertyValue("protected", "hello");
		
		$this->assertFalse($result);
		$this->assertEquals("bar", $this->object->getProtected());
	}
	
	function test_setPublicPropertyValues() {
		$this->object->setPublicPropertyValues(array(
			"public" => "akane",
			"protected" => "daisuke",
			"private" => "morita",
			"noSuchName" => "piggydb"
		));
		
		$this->assertEquals("akane", $this->object->public);
		$this->assertEquals("bar", $this->object->getProtected());
		$this->assertEquals("baz", $this->object->getPrivate());
	}
}


// ValuesViaGettersTest

class ObjectImpl2 extends Object
{
	private $private;
	
	function getName() {
		return "Akane";
	}
	
	function isBeautiful() {
		return TRUE;
	}
	
	function greet() {
		return "Hello";
	}
	
	function getBirthMonth() {
		return 6;
	}
	
	function getNull() {
		return NULL;
	}
	
	function get() {
		return "No property name";
	}
	
	function getAttribute($name) {
		return $name;
	}
	
	private function getSpouse() {
		return "Daisuke";
	}
}

class GetterTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		$this->object = new ObjectImpl2();
	}
	
	function test_getGetter() {
		$getter = $this->object->getGetter("name");
		$this->assertEquals("getName", $getter->getName());
	}
	
	function test_getCamelCaseGetter() {
		$getter = $this->object->getGetter("birthMonth");
		$this->assertEquals("getBirthMonth", $getter->getName());
	}
	
	function test_getGetterWithPrefixIs() {
		$getter = $this->object->getGetter("beautiful");
		$this->assertEquals("isBeautiful", $getter->getName());
	}
	
	function test_getPrivateGetter() {
		$this->assertNull($this->object->getGetter("spouse"));
	}
	
	function test_getUnexistingGetter() {
		$this->assertNull($this->object->getGetter("unexisting"));
	}
	
	function test_getInvalidGetterWithOneArg() {
		$this->assertNull($this->object->getGetter("attribute"));
	}
	
	function test_getValueViaGetter() {
		$this->assertEquals("Akane", $this->object->getValueViaGetter("name"));
	}
	
	function test_getValueViaUnexistingGetter() {
		$this->assertNull($this->object->getValueViaGetter("unexisting"));
	}
	
	function test_getValuesViaGetters() {
		$this->assertEquals(
			array(
				"name" => "Akane", 
				"beautiful" => TRUE,
				"birthMonth" => 6,
				"null" => NULL), 
			$this->object->getValuesViaGetters());
	}
	
	function test_accessGetterViaProperty() {
		$this->assertEquals("Akane", $this->object->name);
		$this->assertEquals(TRUE, $this->object->beautiful);
		$this->assertEquals(6, $this->object->birthMonth);
		
		$this->setExpectedException('AccessorNotFoundException');
		$this->object->unexisting;
	}
	
	function test_accessPrivatePropertyWithoutGetter() {
		$this->setExpectedException('AccessorNotFoundException');
		$this->object->private;
	}
}


// ValueTreeTest

class ObjectImpl3 extends Object
{
	private $content;
	
	function __construct($content = NULL) {
		$this->setContent($content);
	}
	
	function getContent() {
		return $this->content;
	}
	
	function setContent($content) {
		$this->content = $content; 
	}
}

class ValueTreeTest extends PHPUnit_Framework_TestCase
{
	function test_toValueTreeWithOneObject() {
		$object = new ObjectImpl3("hello");
		$values = $object->toValueTree();
		
		$this->assertEquals(array("content" => "hello"), $values);
	}
	
	function test_toValueTreeWithTwoObject() {
		$object2 = new ObjectImpl3("end");
		$object1 = new ObjectImpl3($object2);
		$values = $object1->toValueTree();
		
		$this->assertEquals(
			array("content" => array("content" => "end")), 
			$values);
	}

	function test_toValueTreeWithLoop() {
		$object3 = new ObjectImpl3();
		$object2 = new ObjectImpl3($object3);
		$object1 = new ObjectImpl3($object2);
		$object3->setContent($object1);
		
		$values = $object1->toValueTree();
		
		$this->assertEquals(
			array("content" => array("content" => array())),
			$values);
	}
	

	function test_toValueTreeWithArray() {
		$array = array("foo", "bar");
		$object = new ObjectImpl3($array);
		
		$values = $object->toValueTree();
		
		$this->assertEquals(
			array("content" => array("foo", "bar")),
			$values);
	}

	function test_toValueTreeWithArrayThatContainsObject() {
		$object2 = new ObjectImpl3("end");
		$array = array($object2);
		$object = new ObjectImpl3($array);
		
		$values = $object->toValueTree();
		
		$this->assertEquals(
			array("content" => array(array("content" => "end"))),
			$values);
	}
}


// SetterTest

class ObjectImpl4 extends Object
{
	private $name;
	private $camelCase;
	
	function setName($name) {
		$this->name = $name;
	}
	
	function getName() { 
		return $this->name; 
	}
	
	function setCamelCase($camelCase) {
		$this->camelCase = $camelCase;
	}
	
	function getCamelCase() { 
		return $this->camelCase; 
	}
	
	function setWithNoArgs() {
	}
	
	private function setInternal($value) {
	}
}

class SetterTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		$this->object = new ObjectImpl4();
	}
	
	function test_getSetter() {
		$method = $this->object->getSetter("name");
		
		$this->assertTrue($method instanceof ReflectionMethod);
		$this->assertEquals("setName", $method->getName());
	}
	
	function test_getCamelCaseSetter() {
		$method = $this->object->getSetter("camelCase");
		
		$this->assertTrue($method instanceof ReflectionMethod);
		$this->assertEquals("setCamelCase", $method->getName());
	}
	
	function test_getUnexistingSetter() {
		$this->assertNull($this->object->getSetter("unexisting"));
	}
		
	function test_getSetterWithNoArgs() {
		$this->assertNull($this->object->getSetter("withNoArgs"));
	}
			
	function test_getPrivateSetter() {
		$this->assertNull($this->object->getSetter("internal"));
	}
	
	function test_setValueViaSetter() {
		$this->object->setValueViaSetter("camelCase", "hello");
		$this->assertEquals("hello", $this->object->getCamelCase());
	}
	
	function test_setNullViaSetter() {
		$this->object->setName("hogehoge");
		$this->object->setValueViaSetter("name", NULL);
		
		$this->assertNull($this->object->getName());
	}
	
	function test_setValuesViaSetters() {
		$this->object->setValuesViaSetters(
			array(
				"name" => "Akane",
				"camelCase" => "test",
				"country" => "Japan"));
			
		$this->assertEquals("Akane", $this->object->getName());
		$this->assertEquals("test", $this->object->getCamelCase());
	}
	
	function test_accessSetterViaProperty() {
		$this->object->name = "Akane";
		$this->assertEquals("Akane", $this->object->getName());
		
		$this->object->camelCase = "test";
		$this->assertEquals("test", $this->object->getCamelCase());
		
		$this->setExpectedException('AccessorNotFoundException');
		$this->object->unexisting = 4649;
	}
}
?>
