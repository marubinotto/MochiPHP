<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/utils/Timestamp.class.php');

class TimestampTest extends PHPUnit_Framework_TestCase
{
	function test_parse() {
		$timestamp = Timestamp::parse("2011-03-02 07:09:22");
		
		$this->assertEquals(2011, $timestamp->getYear());
		$this->assertEquals(3, $timestamp->getMonth());
		$this->assertEquals(2, $timestamp->getDayOfMonth());
		$this->assertEquals(7, $timestamp->getHourOfDay());
		$this->assertEquals(9, $timestamp->getMinute());
		$this->assertEquals(22, $timestamp->getSecond());
	}
	
	function test_date() {
		$timestamp = Timestamp::date(1975, 8, 1);
		$this->assertEquals("1975/08/01", $timestamp->format("Y/m/d"));
		
		$timestamp = Timestamp::date("1971", "6", "14");
		$this->assertEquals("1971/06/14", $timestamp->format("Y/m/d"));
	}
	
	function test_format() {
		$timestamp = Timestamp::parse("2011-03-02 07:09:22");
		
		$this->assertEquals("2011/03/02", $timestamp->format("Y/m/d"));
	}
	
	function test_now() {
		$timestamp = new Timestamp();
		// $this->assertEquals("", $timestamp->format("Y/m/d"));
	}
	
	function test_addDays() {
		$timestamp = Timestamp::parse("2011-03-01");		
		$this->assertEquals("2011/03/02", $timestamp->addDays(1)->format("Y/m/d"));
		$this->assertEquals("2011/02/28", $timestamp->addDays(-1)->format("Y/m/d"));
	}
	
	function test_addMonths() {
		$timestamp = Timestamp::parse("2011-01-01");
		$this->assertEquals("2011/02/01", $timestamp->addMonths(1)->format("Y/m/d"));
		$this->assertEquals("2010/12/01", $timestamp->addMonths(-1)->format("Y/m/d"));
	}
}
?>
