<?php
require_once('mochi/controls/RegexField.class.php');

class RegexFieldTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		Control::setMessages(NULL);
		$this->object = new RegexField("field-name");
	}
	
	const URL_PATTERN = '/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/';
	
	function test_validatePattern() {
		$this->object->setPattern(self::URL_PATTERN);
		$this->object->setHintMessage('should be a URL');
		$this->object->setRawValue("http://piggydb.net/");
		
		$this->object->validate();
		
		$this->assertTrue($this->object->isValid());
	}
		
	function test_validatePattern_error() {
		$this->object->setPattern(self::URL_PATTERN);
		$this->object->setHintMessage('should be a URL');
		$this->object->setRawValue("hogehoge");
		
		$this->object->validate();
		
		$this->assertFalse($this->object->isValid());
		$this->assertEquals(
			"field-error-pattern {'field-name.display-name', 'should be a URL'}", 
			$this->object->getError());
	}
	
	function test_emptyStringIsValid() {
		$this->object->setPattern(self::URL_PATTERN);
		$this->object->setRawValue("");
		
		$this->object->validate();
		
		$this->assertTrue($this->object->isValid());
	}
	
	function test_mailPattern() {
		$this->object->setPattern(RegexField::PATTERN_MAIL);
		
		$this->object->setError(NULL);
		$this->object->setRawValue("not-mail-address");
		$this->object->validate();
		$this->assertFalse($this->object->isValid());
		
		$this->object->setError(NULL);
		$this->object->setRawValue("daisuke.marubinotto@gmail.com");
		$this->object->validate();
		$this->assertTrue($this->object->isValid());
	}
	
	function test_alphanumericPattern() {
		$this->object->setPattern(RegexField::PATTERN_ALPHANUMERIC);
		
		$this->object->setError(NULL);
		$this->object->setRawValue("abc123");
		$this->object->validate();
		$this->assertTrue($this->object->isValid());
		
		$this->object->setError(NULL);
		$this->object->setRawValue("abc-123");
		$this->object->validate();
		$this->assertFalse($this->object->isValid());
	}
	
	function test_decimalPattern() {
		$this->object->setPattern(RegexField::PATTERN_DECIMAL);
		
		$this->object->setError(NULL);
		$this->object->setRawValue("123");
		$this->object->validate();
		$this->assertTrue($this->object->isValid());
		
		$this->object->setError(NULL);
		$this->object->setRawValue("123abc");
		$this->object->validate();
		$this->assertFalse($this->object->isValid());
		
		$this->object->setError(NULL);
		$this->object->setRawValue("123.45");
		$this->object->validate();
		$this->assertTrue($this->object->isValid());
		
		$this->object->setError(NULL);
		$this->object->setRawValue(".123.45");
		$this->object->validate();
		$this->assertFalse($this->object->isValid());
	}
	
	function test_zenkakuKatakanaPattern() {
		$this->object->setPattern(RegexField::PATTERN_ZENKAKU_KATAKANA_UTF8);
		
		$this->object->setError(NULL);
		$this->object->setRawValue("カタカナダヨ");
		$this->object->validate();
		$this->assertTrue($this->object->isValid());
		
		$this->object->setError(NULL);
		$this->object->setRawValue("ひらがなだよ");
		$this->object->validate();
		$this->assertFalse($this->object->isValid());
		
		$this->object->setError(NULL);
		$this->object->setRawValue(
			"ァィゥェォアイウエオカキクケコガギグゲゴサシスセソザジズゼゾタチツテトダヂヅデドッナニヌネノ" .
			"ハヒフヘホバビブベボパピプペポマミムメモャュョヤユヨラリルレロワヲン");
		$this->object->validate();
		$this->assertTrue($this->object->isValid());
		
		$this->object->setError(NULL);
		$this->object->setRawValue("ﾊﾝｶｸ");
		$this->object->validate();
		$this->assertFalse($this->object->isValid());
		
		$this->object->setError(NULL);
		$this->object->setRawValue("ジュース");
		$this->object->validate();
		$this->assertTrue($this->object->isValid());
	}
}
?>
