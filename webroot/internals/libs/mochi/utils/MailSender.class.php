<?php
require_once('qdmail.php');
require_once('qdsmtp.php');
require_once('SmartyUtils.class.php');

/**
 * Qdmail - http://hal456.net/qdmail/
 */
class MailSender
{
	private $fromAddress;
	private $fromName;
	
	private $templateDir;
	
	private $smtpSettings;
	
	private $errorDisplay = TRUE;
	
	private $logDir;
	private $logLevel;
	
	private $errorLogDir;
	private $errorLogLevel;
	
	function setFromAddress($fromAddress) { 
		$this->fromAddress = $fromAddress; 
	}
	
	function getFromAddress() { 
		return $this->fromAddress; 
	}
	
	function setFromName($fromName) { 
		$this->fromName = $fromName; 
	}
	
	function getFromName() { 
		return $this->fromName; 
	}
		
	function setTemplateDir($templateDir) { 
		$this->templateDir = $templateDir; 
	}
	
	function setSmtpSettings(array $smtpSettings) { 
		$this->smtpSettings = $smtpSettings; 
	}
	
	function setSmtpSettingsViaAppSettings(array $settings) {
		$protocol = $settings['smtp.protocol'];
		$commonSettings = array(
			'host' => $settings['smtp.host'], 
			'port' => $settings['smtp.port'], 
			'from' => $settings['smtp.from'], 
			'protocol' => $protocol);
		
		if ($protocol == 'POP_BEFORE') {
			$this->setSmtpSettings($commonSettings + array( 
				'pop_host' => $settings['smtp.pop-host'], 
				'pop_user' => $settings['smtp.pop-user'],
				'pop_pass' => $settings['smtp.pop-password']
			));
		}
		else if ($protocol == 'SMTP_AUTH') {		
			$this->setSmtpSettings($commonSettings + array(
				'user' => $settings['smtp.user'], 
				'pass' => $settings['smtp.password']
			));
		}
		else {
			$this->setSmtpSettings($commonSettings);
		}
	}
	
	function setErrorDisplay($errorDisplay) { 
		$this->errorDisplay = $errorDisplay; 
	}
	
	function setLogDir($logDir) { 
		$this->logDir = $logDir; 
	}
	
	function setLogLevel($logLevel) { 
		$this->logLevel = $logLevel; 
	}
	
	function setErrorLogDir($errorLogDir) { 
		$this->errorLogDir = $errorLogDir; 
	}
	
	function setErrorLogLevel($errorLogLevel) { 
		$this->errorLogLevel = $errorLogLevel; 
	}
	
	function send($toAddress, $toName, $subject, $text) {
		$mail = new Qdmail();
		$mail->unitedCharset('UTF-8');
		
		$mail->errorDisplay($this->errorDisplay);
		
		// send log
		$mail->logFilename('qbmail.log');
		if (!is_null($this->logDir)) $mail->logPath($this->logDir);
		if (!is_null($this->logLevel)) $mail->logLevel($this->logLevel);
		
		// error log
		$mail->errorlogFilename('qbmail_error.log');
		if (!is_null($this->errorLogDir)) $mail->errorlogPath($this->errorLogDir);
		if (!is_null($this->errorLogLevel)) $mail->errorlogLevel($this->errorLogLevel);
			
		if (!is_null($this->smtpSettings)) {
			$mail->smtp(true);
			$mail->smtpServer($this->smtpSettings);
		}
		
		$mail->from($this->fromAddress, $this->fromName);
		$mail->to($toAddress, $toName);
		$mail->subject($subject);
		$mail->text($text);
		
		if (is_array($toAddress)) {
			$mail->toSeparate(true);
		}
		
		return $mail->send();
	}
	
	function sendWithTemplate($toAddress, $toName, $subject, $templateName, $values) {
		$model = new TemplateModel();
		$model->setTemplateDir($this->templateDir);
		$model->setTemplateName($templateName);
		
		foreach ($values as $name => $value) 
			$model->put($name, $value);
		
		return $this->send($toAddress, $toName, $subject, $model->renderAsString());
	}
}
?>
