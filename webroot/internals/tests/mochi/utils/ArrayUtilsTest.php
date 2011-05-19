<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/utils/ArrayUtils.class.php');

class ArrayUtilsTest extends PHPUnit_Framework_TestCase
{
	// isIndexed
	
	function test_isIndexed() {
		$this->assertTrue(ArrayUtils::isIndexed(array("foo", "bar")));
		$this->assertFalse(ArrayUtils::isIndexed(array("key" => "value")));
	}
	
	// indexedArrayToString
	
	function test_indexedArrayToString() {
		$this->assertEquals(
			"{'hogehoge', 4649}", 
			ArrayUtils::indexedArrayToString(array("hogehoge", 4649)));
	}
	
	function test_nestedIndexedArrayToString() {
		$this->assertEquals(
			"{'foo', {'bar', 'baz'}}", 
			ArrayUtils::indexedArrayToString(array("foo", array("bar", "baz"))));
	}
	
	// associativeArrayToString
	
	function test_associativeArrayToString() {
		$this->assertEquals(
			"{key => 'value'}", 
			ArrayUtils::associativeArrayToString(array("key" => "value")));
	}
	
	// toString
	
	function test_toString() {
		$this->assertEquals(
			"{'hogehoge', 4649}", ArrayUtils::toString(array("hogehoge", 4649)));
		
		$this->assertEquals(
			"{key => 'value'}", ArrayUtils::toString(array("key" => "value")));
	}
	
	function test_nestedArrayToString() {
		$this->assertEquals(
			"{key => {'value1', 'value2'}}", 
			ArrayUtils::toString(array("key" => array("value1", "value2"))));
	}
}
?>
