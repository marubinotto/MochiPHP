<?php
require_once('Object.class.php');

class Timestamp extends Object
{
	const MINUTE = 60;
	const HOUR = 3600;
	const DAY = 86400;
	
	// number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT). 
	private $timestamp;
	private $elements;
	
	/**
	 * examples:
	 * "2011-03-02 07:09:22"
	 */
	static function parse($string) {
		return new Timestamp(strtotime($string));
	}
	
	static function date($year, $month, $dayOfMonth) {
		return new Timestamp(mktime(0, 0, 0, $month, $dayOfMonth, $year));
	}
	
	function __construct($timestamp = NULL) {
		if (is_null($timestamp)) $timestamp = time();
		$this->timestamp = $timestamp;
		$this->elements = getdate($timestamp);
	}
	
	function getYear() {
		return $this->elements['year'];
	}
	
	function getMonth() {
		return $this->elements['mon'];
	}
	
	function getDayOfMonth() {
		return $this->elements['mday'];
	}
	
	function getHourOfDay() {
		return $this->elements['hours'];
	}
	
	function getMinute() {
		return $this->elements['minutes'];
	}
	
	function getSecond() {
		return $this->elements['seconds'];
	}
	
	function format($pattern) {
		return date($pattern, $this->timestamp);
	}
	
	function addDays($days) {	
		return new Timestamp($this->timestamp + ($days * self::DAY));
	}
	
	function addMonths($months) {
		return new Timestamp(mktime(
			$this->getHourOfDay(), 
			$this->getMinute(), 
			$this->getSecond(), 
			$this->getMonth() + $months, 
			$this->getDayOfMonth(), 
			$this->getYear()));
	}
}
?>
