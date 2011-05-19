<?php
require_once('StringUtils.class.php');
require_once('ArrayUtils.class.php');

class Messages
{
	private $messages;
	
	function __construct(array $messages = array()) {
		$this->messages = $messages;
	}
	
	function get($name, array $args = NULL) {
		$message = $this->messages[$name];
		if (is_null($message)) {
			return self::createDefaultMessage($name, $args);
		}
		
		if (is_null($args)) return $message;	
		array_unshift($args, $message);
		return call_user_func_array('sprintf', $args);
	}
	
	static private function createDefaultMessage($name, array $args = NULL) {
		$message = $name;
		if (!is_null($args)) $message .= (' ' . ArrayUtils::toString($args));
		return  $message;
	}
}
?>
