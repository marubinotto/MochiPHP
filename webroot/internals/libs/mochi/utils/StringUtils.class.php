<?php
class StringUtils
{
	static function length($string) {
		return mb_strlen($string);
	}
	
	static function isEmpty($string) {
		if (is_null($string)) return true;
		if (mb_strlen($string) === 0) return true;
		return false;
	}
	
	static function isBlank($string) {
		if (is_null($string)) return true;
		if (self::isEmpty(trim($string))) return true;
		return false;
	}
	
	static function startsWith($string, $prefix){
    	return mb_strpos($string, $prefix) === 0;
	}
	
	static function endsWith($string, $suffix){
		return mb_strrpos($string, $suffix) === self::length($string) - self::length($suffix);
	}
	
	static function toLowerCaseFirstChar($string) {
		return strtolower(substr($string, 0, 1)) . substr($string, 1);
	}
	
	static function toUpperCaseFirstChar($string) {
		return strtoupper(substr($string, 0, 1)) . substr($string, 1);
	}
	
	static function removeStart($string, $remove) {
		if (!self::startsWith($string, $remove)) return $string;
		return mb_substr($string, self::length($remove));
	}
	
	static function removeEnd($string, $remove) {
		if (!self::endsWith($string, $remove)) return $string;
		return mb_substr($string, 0, self::length($string) - self::length($remove));
	}
	
	static function right($string, $length) {
		if (self::length($string) <= $length) return $string;
		return mb_substr($string, self::length($string) - $length);
	}
	
	static function split($string, $delimiter) {
		return explode($delimiter, $string);
	}
	
	static function createRandomString(
		$length, $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789") {
			
    	// As of PHP 4.2.0, there is no need to seed the random number generator with 
    	// srand() or mt_srand() as this is now done automatically. 
		// mt_srand();
    	
    	$string = "";
    	for($i = 0; $i < $length; $i++)
        	$string .= $chars{mt_rand(0, strlen($chars) - 1)};
		return $string;
	}
	
	static function encryptPassword($password, $saltLength = 8) {
		$salt = self::createRandomString($saltLength);
		return self::encryptPasswordWithSalt($password, $salt);
	}
	
	private static function encryptPasswordWithSalt($password, $salt) {
		return sha1($password . $salt) . $salt;
	}
	
	static function validatePassword($password, $encrypted, $saltLength = 8) {
		$salt = self::right($encrypted, $saltLength);
		return self::encryptPasswordWithSalt($password, $salt) === $encrypted;
	}
}
?>