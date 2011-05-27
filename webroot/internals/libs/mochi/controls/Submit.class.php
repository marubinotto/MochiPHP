<?php
require_once('Field.class.php');

class Submit extends Field
{
	private $clicked = FALSE;
	private $onClick;
	
	function __construct($name, $listener, array $attributes = NULL) {
		parent::__construct($name, $attributes);
		$this->setListenerOnClick($listener);
	}
	
	function isClicked() {
		return $this->clicked;
	}
	
	function setListenerOnClick($listener) {
		$this->onClick = $listener;
	}
	
	function getType() {
		return 'submit';
	}
	
	function setState(Context $context) {
		parent::setState($context);
		if (!is_null($this->getRawValue())) {
			$this->clicked = TRUE;
		}
	}
	
	function validate() {
	}
	
	function dispatchEvent(Context $context) {
		if ($this->isClicked() && !is_null($this->onClick))
				return $this->onClick->invoke($this, $context);
		return TRUE;
	}
	
	function render() {
		$html = new HtmlBuilder();
		$html->startTag('input')
			->attrRaw('type', $this->getType())
			->attr('name', $this->getName())
			->attr('id', $this->getId())
			->attr('value', $this->getDisplayName());
		$html->endEmptyElement();
		return $html->getHtml();
	}
}
?>
