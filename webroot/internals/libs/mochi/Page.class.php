<?php
require_once('utils/Object.class.php');
require_once('utils/StringUtils.class.php');
require_once('utils/ArrayUtils.class.php');
require_once('utils/SmartyUtils.class.php');
require_once('utils/Messages.class.php');
require_once('EventListener.class.php');

abstract class Page extends Object
{
	public $controls = array();
	
	private $messages;
	private $factory;
	private $model;
	private $templateName;
	private $redirect;
	
	function __construct() {
		$this->model = new TemplateModel();
		$this->model->setForHtml();
	}
	
	function setMessages($messages) { 
		$this->messages = $messages; 
	}
	
	function getMessage($name, array $args = NULL) {
		if (is_null($this->messages)) $this->messages = new Messages();
		return $this->messages->get($name, $args);
	}
	
	function setFactory($factory) {
		$this->factory = $factory;
	}
	
	function getFactory() {
		return $this->factory;
	}
	
	function addControl(Control $control) {
		$this->controls[$control->getName()] = $control;
		$this->addModel($control->getName(), $control);
	}
	
	function addModel($name, $value) {
		$this->model->put($name, $value);
	}
	
	function getModel() {
		return $this->model->getValues();
	}
	
	function setTemplateName($templateName) {
		$this->templateName = $templateName;
	}
	
	function setRedirect($resourcePath) {
		$this->redirect = $resourcePath;
	}
	
	function setRedirectToSelf($context) {
		$this->setRedirect($context->getResourcePath()->getPath());
	}
	
	function getRedirect() {
		return $this->redirect;
	}
	
	function onPermissionCheck(Context $context) {
		return TRUE;
	}

	function onPrepare(Context $context) {
		foreach ($this->controls as $control) {
			$control->onPrepare($context);
		}
	}
	
	function listenVia($methodName) {
		return new EventListener($this, $methodName);
	}
		
	function processRequest(Context $context) {
		foreach ($this->controls as $control) {
			$control->setState($context);
		}
		foreach ($this->controls as $control) {
			$continue = $control->dispatchEvent($context);
			if (!$continue) return FALSE;
		}
		return TRUE;
	}

	function render(Context $context) {
		if (StringUtils::isBlank($this->templateName)) return;
		
		$this->onRender($context);
		
		$this->addModel('basePath', $context->getBasePath());
		$this->addModel('resourcePath', $context->getResourcePath());
		
		// for debug
		/*
		$this->addModel('modelNames', 	// put this the first to exclude the debug info below
			ArrayUtils::indexedArrayToString(array_keys($this->getModel())));
		$this->addModel('parameters', 
			ArrayUtils::toString($context->getParameters()));
		$this->addModel('sessionAttrNames', 
			ArrayUtils::indexedArrayToString($context->getSession()->getAttributeNames()));
		*/
			
		$this->model->setTemplateDir($context->getAppResources()->getTemplateDirPath());
		$this->model->setTemplateName($this->templateName);
		$this->model->render();
	}
	
	function onRender(Context $context) {
		foreach ($this->controls as $control) {
			$control->onRender($context);
		}
	}
}
?>
