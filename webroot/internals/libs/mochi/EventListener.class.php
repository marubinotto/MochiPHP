<?php
require_once('utils/Object.class.php');
require_once('utils/StringUtils.class.php');

class EventListener extends Object
{
	private $object;
	private $methodName;
	
	function __construct($object, $methodName) {
		$this->setListener($object, $methodName);
	}
	
	function setListener($object, $methodName) {
		$this->object = $object;
		$this->methodName = $methodName;
		
		if (!method_exists($this->object, $this->methodName)) {
			throw new Exception(
				'Invalid listener: ' . get_class($this->object) . '#' . $this->methodName);
		}
	}
	
	function invoke($source, $context) {
		return call_user_func(
			array($this->object, $this->methodName), 
			$source, $context);
	}
}
?>
