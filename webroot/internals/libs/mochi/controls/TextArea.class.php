<?php
require_once('Field.class.php');
require_once(dirname(__FILE__) . '/../utils/StringUtils.class.php');
require_once(dirname(__FILE__) . '/../utils/HtmlBuilder.class.php');

class TextArea extends Field
{
	private $cols = 20;
	private $rows = 3;
		
	function __construct($name, array $attributes = NULL) {
		parent::__construct($name, $attributes);
	}
	
	function setCols($cols) {
		$this->cols = $cols;
	}
	
	function setRows($rows) {
		$this->rows = $rows;
	}
	
	function validate() {
		if ($this->isRequired() && StringUtils::isEmpty($this->getRawValue())) {
			$this->setErrorMessage('field-error-required');
			return;
		}
	}
	
	function render() {
		$html = new HtmlBuilder();
		$html->startTag('textarea')
			->attr('name', $this->getName())
			->attr('id', $this->getId())
			->attr('cols', $this->cols)
			->attr('rows', $this->rows);		
		if (!$this->isValid()) $html->attrRaw('class', 'error');	
		$html->closeTag();
		
		if (!is_null($this->getRawValue()))
			$html->appendEscaped($this->getRawValue());
		
		$html->endTag('textarea');
		return $html->getHtml();
	}
}
?>
