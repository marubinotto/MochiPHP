<?php
require_once('Field.class.php');
require_once(dirname(__FILE__) . '/../utils/StringUtils.class.php');
require_once(dirname(__FILE__) . '/../utils/HtmlBuilder.class.php');

class Radio extends Field
{
	private $radioGroup;
	private $checked = FALSE;
	
	function __construct($value, $displayName = NULL, array $attributes = NULL) {
		parent::__construct(NULL, $attributes);
		
		$this->setRawValue($value);
		if (!is_null($displayName)) $this->setDisplayName($displayName);
	}
	
	function setRadioGroup($radioGroup) { 
		$this->radioGroup = $radioGroup; 
	}
	
	function getForm() {
		if (!is_null(parent::getForm())) return parent::getForm();
		if (!is_null($this->radioGroup)) return $this->radioGroup->getForm();
		return NULL;
	}
	
	function getName() {
		if (!is_null($this->radioGroup))
			return $this->radioGroup->getName();
		else
			return parent::getName();
	}
	
	function getId() {
		if (!is_null($this->id)) return $this->id;
		
		$id = parent::getId();
		if (is_null($id)) return NULL;
		return $id . '_' . $this->getRawValue();
	}
	
	function isChecked() {
		return $this->checked;
	}
	
	function setChecked($checked) {
		$this->checked = $checked;
	}
	
	function setCheckedByValue($value) {
		$this->setChecked($value == $this->getRawValue());
		return $this->isChecked();
	}
	
	function setState(Context $context) {
		$value = $context->getParameter($this->getName());
		$this->setCheckedByValue($value);
	}
	
	function validate() {
	}
	
	function render() {
		$html = new HtmlBuilder();
		$html->startTag('input')
			->attrRaw('type', 'radio')
			->attr('name', $this->getName())
			->attr('id', $this->getId())
			->attr('value', $this->getRawValue());		
		if ($this->isChecked()) $html->attrRaw('checked', 'checked');
		if (!$this->isValid()) $html->attrRaw('class', 'error');	
		$html->endEmptyElement();
		
		$html->tagWithText('label', $this->getDisplayName(), array('for' => $this->getId()));
		
		return $html->getHtml();
	}
}

class RadioGroup extends Field
{
	public $radios = array();
	
	private $vertical = FALSE;
	
	function __construct($name, array $attributes = NULL) {
		parent::__construct($name, $attributes);
	}
	
	function add(Radio $radio) {
		$radio->setRadioGroup($this);
		$this->radios[] = $radio;
	}
	
	function addAll(array $options) {
		foreach ($options as $value => $displayName) {
			$this->add(new Radio($value, $displayName));
		}
	}
	
	function select($value) {
		$this->setRawValue($value);
		foreach ($this->radios as $radio) {
			$radio->setCheckedByValue($this->getRawValue());
		}
	}
	
	function getSelectedDisplayName() {
		foreach ($this->radios as $radio) {
			if ($radio->setCheckedByValue($this->getRawValue())) {
				return $radio->getDisplayName();
			}
		}
		return NULL;
	}
	
	function setState(Context $context) {
		foreach ($this->radios as $radio) {
			$radio->setState($context);
		}
		parent::setState($context);
	}
	
	function validate() {
		if ($this->isRequired() && StringUtils::isEmpty($this->getRawValue())) {
			$this->setErrorMessage('field-error-not-selected');
			return;
		}
	}
		
	function setVerticalLayout($vertical) {
		$this->vertical = $vertical;
	}
	
	function render() {
		$html = new HtmlBuilder();
		$html->startTagWithAttrs('span', array('id' => $this->getId()))
			->closeTag()->newline();		
		foreach ($this->radios as $index => $radio) {
			if ($this->vertical && $index > 0) {
				$html->append('<br/>')->newline();
			}
			$radio->setCheckedByValue($this->getRawValue());
			$html->append($radio->render())->newline();
		}
		$html->endTag('span');
		return $html->getHtml();
	}
}
?>
