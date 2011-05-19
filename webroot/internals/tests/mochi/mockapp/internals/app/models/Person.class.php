<?php
class Person
{
	private $message;
	
	function __construct($message = 'Good afternoon!') {
		$this->message = $message;
	}
	
	function greet() {
		return $this->message;
	}
}
?>
