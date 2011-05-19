<?php
require_once(dirname(__FILE__) . '/../Control.class.php');
require_once(dirname(__FILE__) . '/../utils/StringUtils.class.php');
require_once('Form.class.php');

/**
 * - Field controls are contained by the Form control
 */
abstract class Field extends Control
{
	private $form;
	
	private $omitId = FALSE;
	
	private $rawValue;
	
	private $error;	
	private $required = FALSE;
	
	function __construct($name, array $attributes = NULL) {
		parent::__construct($name, $attributes);
	}
	
	function setForm($form) {
		$this->form = $form;
	}
	
	function getForm() {
		return $this->form;
	}
	
	function getId() {
		if ($this->omitId) return NULL;
		if (!is_null(parent::getId())) return parent::getId();
		if (is_null($this->getName())) return NULL;
		
		$form = $this->getForm();
		$prefix = !is_null($form) ? $form->getId() . '_' : '';
		return $prefix . $this->getName();
	}
	
	function setOmitId($omitId) {
		$this->omitId = $omitId;
	}
	
	function getValue() {
		$rawValue = $this->getRawValue();
		return StringUtils::isEmpty($rawValue) ? NULL : $rawValue;
	}
	
	function setValue($value) {
		$this->rawValue = $value;
	}
	
	function getRawValue() {
		return $this->rawValue;
	}
	
	function setRawValue($rawValue) {
		$this->rawValue = $rawValue;
	}
		
	function setState(Context $context) {
		$this->setRawValue($context->getParameter($this->getName()));
		$this->validate();
	}
	
	function restoreState($values) {
		if (isset($values[$this->getName()])) {
			$this->setRawValue($values[$this->getName()]);
		}
		else {
			$this->setRawValue(NULL);
		}
	}

	function setError($error) {
		$this->error = $error;
	}
	
	function getError() {
		return $this->error;
	}
	
	function setErrorMessage($name, array $args = array()) {
		array_unshift($args, $this->getDisplayName());
		$message = $this->getMessage($name, $args);
		$this->setError($message);
	}
	
	function isValid() {
		return is_null($this->error);
	}
	
	function setRequired($required) {
		$this->required = $required;
	}
	
	function isRequired() {
		return $this->required;
	}
	
	abstract protected function validate();
	
	function renderAsHidden() {
		$html = new HtmlBuilder();
		HiddenField::renderHiddenField(
			$html, $this->getName(), $this->getRawValue(), $this->getId());
		return $html->getHtml();
	}
}
?>
