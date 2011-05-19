<?php
require_once('PHPUnit/Framework.php');

class ExperimentTest extends PHPUnit_Framework_TestCase
{
	function test_nothing() {
		$this->assertTrue(TRUE);
	}
	
	function test_substr() {
		$this->assertEquals("", substr("get", 3));
	}
	
	function test_array() {
		$array = array("foo" => 1, "bar" => 2);
		$this->modifyArray($array);
		// $this->assertEquals("", var_export($array, TRUE));
	}
	
	private function modifyArray(&$array) {
		unset($array["foo"]);
		$array["bar"] = "updated";
	}
	
	function test_assert() {
		assert_options(ASSERT_ACTIVE, 1);
		assert_options(ASSERT_WARNING, 0);
		assert_options(ASSERT_QUIET_EVAL, 1);
		
		function my_assert_handler($file, $line, $code) {
			throw new Exception("Assertion Failed: <$code> $file ($line)");
		}

		assert_options(ASSERT_CALLBACK, 'my_assert_handler');
		
		$this->setExpectedException('Exception');
		assert('false /* not yet implemented */');
	}
	
	function test_classNotFound() {
		$this->setExpectedException('ReflectionException');
		new ReflectionClass("NoSuchClass");
	}
	
	function test_getConstantViaReflection() {
		$class = new ReflectionClass("ExperimentObject");
		
		$this->assertEquals("hello", $class->getConstant("MESSAGE"));
		$this->assertFalse($class->getConstant("NO_SUCH_CONSTANT"));
	}
	
	function test_splitCamelCase() {
		$regex = '/(?!^)[[:upper:]]/';
		$this->assertEquals("foo", preg_replace($regex,' \0', "foo"));
		$this->assertEquals("Camel Case Class Name", preg_replace($regex,' \0', "CamelCaseClassName"));
		$this->assertEquals("method Name", preg_replace($regex,' \0', "methodName"));
	}
	
	function test_globalSet() {
		global $TEST_MESSAGE;
		$TEST_MESSAGE = "hello";
		$this->assertEquals("hello", $TEST_MESSAGE);
	}
	
	function test_globalGet() {
		global $TEST_MESSAGE;
		$this->assertFalse(isset($TEST_MESSAGE));
	}
	
	function test_getMethod() {
		$object = new ExperimentObject();
		$class = new ReflectionClass($object);
		
		$method = $class->getMethod("camelCase");
		$this->assertEquals("camelCase", $method->getName());
		
		$method = $class->getMethod("CAMELCASE");
		$this->assertEquals("camelCase", $method->getName());
	}
	
	function test_accessUnexistingProperty() {
		$this->assertFalse(isset($this->unexisting));
		$this->assertTrue(is_null($this->unexisting));
		
		$this->unexisting = "value";
		
		$this->assertTrue(isset($this->unexisting));
		$this->assertFalse(is_null($this->unexisting));
		$this->assertEquals("value", $this->unexisting);
	}
	
	function test_intval() {
		$this->assertEquals(2, intval(2.5));
	}
}

class ExperimentObject
{
	const MESSAGE = "hello";
	
	function camelCase() {
	}
}
?>
