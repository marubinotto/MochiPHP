<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/utils/MailSender.class.php');

class SmtpMailSenderTest extends PHPUnit_Framework_TestCase
{
	const ENABLED = false;
	const FROM = '';
	const PASSWORD = '';
	const TO_SINGLE = '';
	static $TO_MULTIPLE = array('', '');
		
	private $object;
	
	function setUp() {
		$this->object = new MailSender();
		$this->object->setFromAddress(self::FROM);
		$this->object->setSmtpSettings(array(
			'host'		 => 'ssl://smtp.gmail.com', 
			'port'     	=> 465, 
			'from'     => self::FROM, 
			'protocol' => 'SMTP_AUTH', 
			'user'     => self::FROM, 
			'pass'     => self::PASSWORD,
		));
	}
	
	function test_dummy() {
	}
	
	function test_send() {
		if (!self::ENABLED) return;
		
		$this->object->setLogDir('C:/Temp/');
		$this->object->setLogLevel(1);
		$this->object->setErrorLogDir('C:/Temp/');
		$this->object->setErrorLogLevel(1);
		
		$this->object->send(
			self::TO_SINGLE, '山田 太郎 様', 
			'送信者名なし', 
			'このメールはPHPUnitによって送信されました。');
	}
		
	function _test_sendWithFromName() {
		if (!self::ENABLED) return;
		
		$this->object->setFromName('送信者名');
		$this->object->send(
			self::TO_SINGLE, '山田 太郎  様', 
			'送信者名あり', 
			'このメールはPHPUnitによって送信されました。');
	}
	
	function _test_send_multiple() {
		if (!self::ENABLED) return;
		
		$this->object->send(
			self::$TO_MULTIPLE, array(NULL, NULL), 
			'一斉配信', 
			'このメールはPHPUnitによって送信されました。');
	}
}
?>
