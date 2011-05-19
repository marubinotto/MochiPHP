<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/EventListener.class.php');

class EventListenerTest extends PHPUnit_Framework_TestCase
{
	function test_isValid() {
		$listener = new EventListener(new Listener(), "onEvent");
	}
		
	function test_isNotValid() {
		$this->setExpectedException('Exception');
		$listener = new EventListener(new Listener(), "onClick");
	}
	
	function test_invoke() {
		$listener = new Listener();
		$object = new EventListener($listener, "onEvent");
		
		$source = new MockObject();
		$context = new MockObject();
		
		$result = $object->invoke($source, $context);
		
		$this->assertTrue($result);
		$this->assertSame($source, $listener->source);
		$this->assertSame($context, $listener->context);
	}
}

class Listener
{
	public $source;
	public $context;
	
	function onEvent($source, $context) {
		$this->source = $source;
		$this->context = $context;
		return TRUE;
	}
}

class MockObject {}
?>
