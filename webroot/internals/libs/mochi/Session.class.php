<?php
require_once('utils/Object.class.php');
require_once('utils/StringUtils.class.php');

class Session extends Object
{
	function __construct() {
		session_start();
	}
	
	function getId() {
		return session_id();
	}
	
	function getAttributeNames() {
		return array_keys($_SESSION);
	}

	function get($name) {
		if (!isset($_SESSION[$name])) return NULL;
		$value = $_SESSION[$name];
		if ($value instanceof FlashAttribute) {
			$value = $value->value;
			$this->remove($name);
		}
		return $value;
	}

	function set($name, $value) {
		return $_SESSION[$name] = $value;
	}
	
	function setFlash($name, $value) {
		$this->set($name, new FlashAttribute($value));
	}

	function remove($name) {
		unset($_SESSION[$name]);
	}
	
	function destroy() {
		$_SESSION = array();
		session_destroy();
	}
	
	// Internals
	
	// If an application is using sessions and uses session_register() to register objects, 
	// these objects are serialized automatically at the end of each PHP page, 
	// and are unserialized automatically on each of the following pages. 
	// - http://www.php.net/manual/en/language.oop5.serialization.php
	
	const PREFIX_SERIALIZED = '__SERIALIZED__';
	
	static function serialize($value) {
		return self::PREFIX_SERIALIZED . serialize($value);
	}
	
	static function isSerialized($value) {
		if (!is_string($value)) return FALSE;
		return StringUtils::startsWith($value, self::PREFIX_SERIALIZED);
	}
	
	static function unserialize($string) {
		assert('self::isSerialized($string)');
		$string = StringUtils::removeStart($string, self::PREFIX_SERIALIZED);
		return unserialize($string);
	}
}

class FlashAttribute
{
	public $value;
	
	function __construct($value) {
		$this->value = $value;
	}
}
?>
