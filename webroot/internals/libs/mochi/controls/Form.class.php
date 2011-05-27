<?php
require_once(dirname(__FILE__) . '/../Context.class.php');
require_once(dirname(__FILE__) . '/../Control.class.php');
require_once(dirname(__FILE__) . '/../utils/Object.class.php');
require_once(dirname(__FILE__) . '/../utils/HtmlBuilder.class.php');
require_once('Field.class.php');

class Form extends Control
{
	const PN_FORM_NAME = '_formName';
	
	private $actionUrl;
	
	private $error;
	private $submitted = FALSE;
	
	public $fields = array();
	
	private $stateful = FALSE;
	private $sessionStateKey;
	
	function __construct($name, array $attributes = NULL) {
		parent::__construct($name, $attributes);
	}
	
	function getId() {
		return is_null(parent::getId()) ? $this->getName() : parent::getId();
	}
	
	function setActionUrl($actionUrl) { 
		$this->actionUrl = $actionUrl; 
	}
	
	function addField(Field $field) {
		$this->fields[$field->getName()] = $field;
		$field->setForm($this);
	}
	
	function addHiddenValue($name, $value) {
		$this->addField(new HiddenField($name, $value));
	}
	
	function getHiddenFields() {
		$fields = array();
		foreach ($this->fields as $field) {
			if ($field instanceof HiddenField) $fields[] = $field;
		}
		return $fields;
	}
	
	function getErrorFields() {
		$fields = array();
		foreach ($this->fields as $field) {
			if (!$field->isValid()) $fields[] = $field;
		}
		return $fields;
	}
	
	function getValue($fieldName) {
		$this->ensureFieldExists($fieldName);
		return $this->fields[$fieldName]->getValue();
	}
	
	private function ensureFieldExists($fieldName) {
		if (!isset($this->fields[$fieldName])) {
			throw new Exception("No such field [{$fieldName}] in the form [{$this->getName()}]");
		}
	}
	
	function getValues() {
		$values = array();
		foreach ($this->fields as $field) {
			$values[$field->getName()] = $field->getValue();
		}
		return $values;
	}
	
	function copyValuesTo(Object $object) {
		$object->bindValues($this->getValues());
	}
	
	function setValue($fieldName, $value) {
		$this->ensureFieldExists($fieldName);
		$this->fields[$fieldName]->setValue($value);
	}
	
	function setValues($values) {
		foreach ($values as $name => $value) {
			if (isset($this->fields[$name])) {
				$this->setValue($name, $value);
			}
		}
	}
	
	function copyValuesFrom(Object $object) {
		$this->setValues($object->toValues());
	}

	function setError($error) {
		$this->error = $error;
	}
	
	function getError() {
		return $this->error;
	}
	
	function isValid() {
		if (!is_null($this->error)) return false;
		
		foreach ($this->fields as $field) {
			if (!$field->isValid()) return false;
		}
		
		return true;
	}
	
	function onPrepare(Context $context) {
		foreach ($this->fields as $field) {
			$field->onPrepare($context);
		}
	}
		
	function setState(Context $context) {
		$this->setActionUrl($context->getRequestUriWithoutQuery());
		
		$this->submitted = 
			($context->getParameter(self::PN_FORM_NAME) === $this->getName());
		if ($this->submitted) {
			$this->storeStateToSession($context);
			foreach ($this->fields as $field) {
				$field->setState($context);
			}
			$this->validate();
		}
		else {
			$this->restoreStateFromSession($context);
		}
	}
	
	private function storeStateToSession(Context $context) {
		if ($this->isStateful()) {
			$context->getSession()->set(
				$this->getSessionStateKey($context), $context->getParameters());
		}
	}
	
	private function restoreStateFromSession(Context $context) {
		if ($this->isStateful()) {
			$values = $context->getSession()->get($this->getSessionStateKey($context));
			if (!is_null($values)) {
				foreach ($this->fields as $field) $field->restoreState($values);
			}
		}
	}
	
	function clearSessionState(Context $context) {
		if ($this->isStateful()) {
			$context->getSession()->remove($this->getSessionStateKey($context));
		}
	}
	
	function setSessionStateKey($sessionStateKey) {
		$this->sessionStateKey = $sessionStateKey;
	}
	
	function getSessionStateKey(Context $context) {
		if (!is_null($this->sessionStateKey)) 
			return $this->sessionStateKey;
		return 'state:' . $context->getResourcePath() . '#' . $this->getName();
	}
	
	function isStateful() {
		return $this->stateful;
	}
	
	function setStateful($stateful) {
		$this->stateful = $stateful;
	}
	
	function isSubmitted() {
		return $this->submitted;
	}
	
	private $onValidSubmission;
	
	function setListenerOnValidSubmission($listener) {
		$this->onValidSubmission = $listener;
	}
	
	function dispatchEvent(Context $context) {
		if ($this->isSubmitted()) {
			foreach ($this->fields as $field) {
				$field->dispatchEvent($context);
			}
			if ($this->isValid()) {
				if (!is_null($this->onValidSubmission))
					return $this->onValidSubmission->invoke($this, $context);
			}
		}
		return TRUE;
	}
	
	/**
	 * A Form subclass can override this method to implement cross-field validation logic. 
	 */
	protected function validate() {
	}
	
	function render() {
		return startTag() . renderErrors() . endTag();
	}
		
	function startTag() {
		$html = new HtmlBuilder();
		$html->startTag('form')
			->attr('name', $this->getName())
			->attr('id', $this->getId())
			->attrRaw('method', 'post')
			->attrRaw('action', $this->actionUrl)
			->closeTag();
		$html->newline();
		
		// "hidden" fields
		HiddenField::renderHiddenField($html, self::PN_FORM_NAME, $this->getName());
		foreach ($this->getHiddenFields() as $hidden) {
			$html->newline();
			$html->append($hidden->render());
		}
		
		return $html->getHtml();
	}
	
	function endTag() {
		return "</form>";
	}
	
	function renderErrors() {
		$html = new HtmlBuilder();
		if ($this->isValid()) return $html->getHtml();
		
		$html->startTag('table')
			->attrRaw('class', 'errors')
			->attr('id', $this->getId() . '-errors')
			->closeTag()->newline();
			
		if (!is_null($this->getError())) {
			$html->startTagWithAttrs('tr', array('class' => 'errors'))->closeTag();
			$html->startTagWithAttrs('td', array('class' => 'errors', 'align' => 'left'))->closeTag();
			$html->newline();
			$html->tagWithText('span', $this->getError(), array('class' => 'error'));
			$html->newline();
			$html->endTag('td')->endTag('tr')->newline();
		}
		
		foreach ($this->getErrorFields() as $field) {
			$html->startTagWithAttrs('tr', array('class' => 'errors'))->closeTag();
			$html->startTagWithAttrs('td', array('class' => 'errors', 'align' => 'left'))->closeTag();
			$html->newline();
			$html->startTag('a')
				->attrRaw('class', 'error')
				->attrRaw('href', "javascript:setFocus('" . $field->getId() . "');")
				->closeTag()
				->appendEscaped($field->getError())
				->endTag('a');
			$html->newline();
			$html->endTag('td')->endTag('tr')->newline();
		}
			
		$html->endTag('table');
		return $html->getHtml();
	}
}

class HiddenField extends Field
{
	function __construct($name, $value = NULL, array $attributes = NULL) {
		parent::__construct($name, $attributes);
		if (!is_null($value)) $this->setRawValue($value);
	}
	
	static function renderHiddenField(HtmlBuilder $html, $name, $value, $id = NULL) {
		$html->startTag('input')
			->attrRaw('type', 'hidden')
			->attrRaw('name', $name)
			->attr('value', $value)
			->attr('id', $id)
			->endEmptyElement();
	}
	
	function validate() {
	}
	
	function render() {
		$html = new HtmlBuilder();
		self::renderHiddenField(
			$html, $this->getName(), $this->getRawValue(), $this->getId());
		return $html->getHtml();
	}
}
?>
