<?php
require_once('TextField.class.php');
require_once(dirname(__FILE__) . '/../utils/StringUtils.class.php');

class RegexField extends TextField
{
	const PATTERN_NUMBER = '/^\d+$/';
	const PATTERN_DECIMAL = '/^([1-9]\d*|0)(\.\d+)?$/';
	const PATTERN_ALPHANUMERIC = '/^[a-zA-Z0-9]+$/';
	const PATTERN_MAIL = 
		'/^[\w!#$%&\'*+\/=?^_{}\\|~-]+([\w!#$%&\'*+\/=?^_{}\\|~\.-]+)*@([\w][\w-]*\.)+[\w][\w-]*$/';
	
	const PATTERN_ZENKAKU_KATAKANA_UTF8 = '/^(?:\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xBE])+$/';
	
	private $pattern;
	private $hintMessage;
	
	function __construct($name, array $attributes = NULL) {
		parent::__construct($name, $attributes);
	}
	
	function setPattern($pattern) { 
		$this->pattern = $pattern; 
	}
	
	function setHintMessage($hintMessage) { 
		$this->hintMessage = $hintMessage; 
	}
	
	function validate() {
		parent::validate();
		if (!$this->isValid()) return;
		
		$value = $this->getRawValue();
		if (StringUtils::isEmpty($value) || is_null($this->pattern)) return;
		
		if (preg_match($this->pattern, $value) === 0) {
			$this->setErrorMessage('field-error-pattern', array($this->hintMessage));
			return;
		}
	}
}
?>
