<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/utils/StringUtils.class.php');
require_once('mochi/utils/ArrayUtils.class.php');

class StringUtilsTest extends PHPUnit_Framework_TestCase
{
	// isEmpty
	
	function test_nullIsEmpty() {
		$this->assertTrue(StringUtils::isEmpty(NULL));
	}
	
	function test_isEmpty() {
		$this->assertTrue(StringUtils::isEmpty(''));
	}
	
	function test_blankIsNotEmpty() {
		$this->assertFalse(StringUtils::isEmpty(' '));
	}
	
	// isBlank
	
	function test_nullIsBlank() {
		$this->assertTrue(StringUtils::isBlank(NULL));
	}
	
	function test_oneSpaceIsBlank() {
		$this->assertTrue(StringUtils::isBlank(' '));
	}
	
	// startsWith
	
	function test_startsWith() {
		$this->assertTrue(StringUtils::startsWith("getValue", "get"));
		$this->assertFalse(StringUtils::startsWith("setValue", "get"));
	}
	
	function test_startsWithJapanese() {
		$this->assertTrue(StringUtils::startsWith("日本語", "日"));
	}
	
	function test_startsWithSameCharAsEnd() {
		$this->assertTrue(StringUtils::startsWith("aa", "a"));
	}
	
	// endsWith
	
	function test_endsWith() {
		$this->assertTrue(StringUtils::endsWith("test.txt", ".txt"));
		$this->assertFalse(StringUtils::endsWith("test.txt", ".pdf"));
	}
	
	function test_endsWithSameCharAsStart() {
		$this->assertTrue(StringUtils::endsWith("aa", "a"));
	}
	
	function test_endsWithJapanese() {
		$this->assertTrue(StringUtils::endsWith("日本語", "語"));
	}
	
	// toLowerCaseFirstChar
	
	function test_toLowerCaseFirstChar() {
		$this->assertEquals("a", StringUtils::toLowerCaseFirstChar("A"));
		$this->assertEquals("textFragment", StringUtils::toLowerCaseFirstChar("TextFragment"));
	}
	
	// toUpperCaseFirstChar
	
	function test_toUpperCaseFirstChar() {
		$this->assertEquals("A", StringUtils::toUpperCaseFirstChar("a"));
		$this->assertEquals("Test", StringUtils::toUpperCaseFirstChar("test"));
	}
	
	// removeStart
	
	function test_removeStart() {
		$this->assertEquals(
			"domain.com", 
			StringUtils::removeStart("www.domain.com", "www."));
	}
		
	function test_removeStartNotMatched() {
		$this->assertEquals(
			"www.domain.com", 
			StringUtils::removeStart("www.domain.com", "foo"));
	}
	
	// removeEnd
	
	function test_removeEnd() {
		$this->assertEquals("www.domain", StringUtils::removeEnd("www.domain.com", ".com"));
		$this->assertEquals("/path/to", StringUtils::removeEnd("/path/to/", "/"));
	}
	
	function test_removeEndNotMatched() {
		$this->assertEquals(
			"www.domain.com", 
			StringUtils::removeEnd("www.domain.com", ".net"));
	}
	
	// right
	
	function test_right() {
		$this->assertEquals("", StringUtils::right("abc", 0));
		$this->assertEquals("bc", StringUtils::right("abc", 2));
		$this->assertEquals("abc", StringUtils::right("abc", 4));
	}
	
	// split
	
	function test_split() {
		$this->assertEquals(
			"{'foo', 'bar', 'hoge'}", 
			ArrayUtils::toString(StringUtils::split("foo-bar-hoge", "-")));
	}
	
	// createRandomString
	
	function test_createRandomString() {
		// $this->assertEquals("", StringUtils::createRandomString(30));
		
		$this->assertEquals(10, strlen(StringUtils::createRandomString(10)));
		$this->assertEquals(20, strlen(StringUtils::createRandomString(20)));
		
		$this->assertEquals("aaaaa", StringUtils::createRandomString(5, "a"));
	}
	
	// encryptPassword
	
	function test_encryptPassword() {
		$encrypted = StringUtils::encryptPassword("password");
		
		$this->assertTrue(StringUtils::validatePassword("password", $encrypted));
		$this->assertFalse(StringUtils::validatePassword("pasword", $encrypted));
	}
}
?>