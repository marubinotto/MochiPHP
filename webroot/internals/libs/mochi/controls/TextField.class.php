<?php
require_once('Field.class.php');
require_once(dirname(__FILE__) . '/../utils/StringUtils.class.php');
require_once(dirname(__FILE__) . '/../utils/HtmlBuilder.class.php');

class TextField extends Field
{
	private $size;
	private $maxLength;
	
	function __construct($name, array $attributes = NULL) {
		parent::__construct($name, $attributes);
	}
	
	function getType() {
		return 'text';
	}
	
	function setSize($size) {
		$this->size = $size;
	}
	
	function setMaxLength($maxLength) {
		$this->maxLength = $maxLength;
	}
	
	function validate() {
		if ($this->isRequired() && StringUtils::isEmpty($this->getRawValue())) {
			$this->setErrorMessage('field-error-required');
			return;
		}
	}
	
	function render() {
		$html = new HtmlBuilder();
		$html->startTag('input')
			->attrRaw('type', $this->getType())
			->attr('name', $this->getName())
			->attr('id', $this->getId())
			->attr('value', $this->getRawValue())
			->attr('size', $this->size)
			->attr('maxlength', $this->maxLength);		
		if (!$this->isValid()) $html->attrRaw('class', 'error');	
		$html->endEmptyElement();
		return $html->getHtml();
	}
}
?>
