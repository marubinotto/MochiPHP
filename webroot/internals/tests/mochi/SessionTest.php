<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/Session.class.php');

class SessionTest extends PHPUnit_Framework_TestCase
{
	function test_serialize() {
		$object = new Exception("hello");
		
		$serialized = Session::serialize($object);
		$this->assertTrue(Session::isSerialized($serialized));
		
		$unserialized = Session::unserialize($serialized);
		$this->assertEquals("hello", $unserialized->getMessage());
	}
	
	function test_inappropriateStringIsNotSerialized() {
		$this->assertFalse(Session::isSerialized("dummy"));
	}
}
?>
