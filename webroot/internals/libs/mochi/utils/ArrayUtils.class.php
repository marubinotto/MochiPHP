<?php
class ArrayUtils
{
	static function isIndexed(array $array) {
		if (count($array) > 0) {
			return isset($array[0]);
		}
		return FALSE;
	}
	
	static function toString(array $array) {
		return self::isIndexed($array) ? 
			self::indexedArrayToString($array) : 
			self::associativeArrayToString($array);
	}
	
	private static function valueToString($value) {
		if (is_string($value)) 
			return "'" . strval($value) . "'";
		else if (is_array($value))
			return self::toString($value);
		else 
			return strval($value);
	}
	
	static function indexedArrayToString(array $array) {
		$string = '{';
		foreach ($array as $index => $value) {
			if ($index > 0) $string .= ', ';
			$string .= self::valueToString($value);
		}
		$string .= '}';
		return $string;
	}
	
	static function associativeArrayToString(array $array) {
		$string = '{';
		$first = TRUE;
		foreach ($array as $key => $value) {
			if ($first) $first = FALSE; else $string .= ', ';
			$string .= ($key . ' => ' . self::valueToString($value));
		}
		$string .= '}';
		return $string;
	}
}

class ArrayWrapper 
{
	private $array;
	
	function __construct(array $array) {
		$this->array = $array;
	}
	
	public function __toString() {
		return ArrayUtils::toString($this->array);
	}
	
	function size() {
		return count($this->array);
	}
	
	function get($key) {
		if (!isset($this->array[$key])) return NULL;
		return $this->array[$key];
	}
}
?>
