<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/Control.class.php');
require_once('mochi/utils/Messages.class.php');

class ControlTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		$this->object = new ControlImpl("control-name");
	}
	
	function test_getDisplayName() {
		Control::setMessages(new Messages(array("title.display-name" => "タイトル")));
		$this->object->setName("title");
		$this->assertEquals("タイトル", $this->object->getDisplayName());
	}
	
	function test_setDisplayName() {
		Control::setMessages(new Messages(array("title.display-name" => "タイトル")));
		$this->object->setName("title");
		$this->object->setDisplayName("Main Title");
		$this->assertEquals("Main Title", $this->object->getDisplayName());
	}
	
	function test_setAttributesViaConstructor() {
		$object = new ControlImpl("name", array("id" => "4649", "foo" => "bar"));
		
		$this->assertEquals("4649", $object->getId());
		$this->assertEquals("bar", $object->foo);
	}
}

class ControlImpl extends Control
{
	public $foo;
	
	function __construct($name, array $attributes = NULL) {
		parent::__construct($name, $attributes);
	}
	
	function render() {
		return "";
	}
	
	function setFoo($foo) { 
		$this->foo = $foo; 
	}
}
?>
