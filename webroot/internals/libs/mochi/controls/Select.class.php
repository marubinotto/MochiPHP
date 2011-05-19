<?php
require_once('Field.class.php');
require_once(dirname(__FILE__) . '/../utils/HtmlBuilder.class.php');

class Select extends Field
{
	public $options = array();		// value => label
	
	function __construct($name, array $attributes = NULL) {
		parent::__construct($name, $attributes);
	}
	
	function add($value, $label) {
		$this->options[$value] = $label;
	}
	
	function addAll(array $options) {
		foreach ($options as $value => $label) {
			$this->add($value, $label);
		}
	}
	
	function validate() {
		// TODO
	}
	
	function render() {
		$html = new HtmlBuilder();
		$html->startTag('select')
			->attr('name', $this->getName())
			->attr('id', $this->getId());
		$html->closeTag()->newline();
		
		foreach ($this->options as $value => $label) {
			$html->startTag('option')->attr('value', $value);
			if ($value == $this->getRawValue()) {
				$html->attrRaw('selected', 'selected');
			}
			$html->closeTag();
			$html->appendEscaped($label);
			$html->endTag('option')->newline();
		}
		
		$html->endTag('select');
		return $html->getHtml();
	}
	
	function renderAsSelected($value) {
		$this->setRawValue($value);
		return $this->render();
	}
}
?>
