<?php
require_once('Field.class.php');

class Checkbox extends Field
{
	const DEFAULT_VALUE = 'on';
	
	function __construct($name, array $attributes = NULL) {
		parent::__construct($name, $attributes);
	}
	
	function getValue() {
		$rawValue = $this->getRawValue();
		if (is_null($rawValue)) return FALSE;
		return $rawValue == self::DEFAULT_VALUE ? TRUE : $rawValue;
	}
	
	function setValue($value) {
		if ($value)
			$this->setRawValue($value === TRUE ? self::DEFAULT_VALUE : $value);
		else
			$this->setRawValue(NULL);
	}
	
	function isChecked() {
		return !is_null($this->getRawValue());
	}
	
	function setChecked($checked) {
		$this->setRawValue($checked ? self::DEFAULT_VALUE : NULL);
	}
	
	function validate() {
		if ($this->isRequired() && !$this->isChecked()) {
			$this->setErrorMessage('field-error-not-checked');
			return;
		}
	}
	
	function render() {
		$html = new HtmlBuilder();
		$html->startTag('input')
			->attrRaw('type', 'checkbox')
			->attr('name', $this->getName())
			->attr('id', $this->getId())
			->attr('value', $this->getRawValue());		
		if ($this->isChecked()) $html->attrRaw('checked', 'checked');
		if (!$this->isValid()) $html->attrRaw('class', 'error');	
		$html->endEmptyElement();
		return $html->getHtml();
	}
}
?>
