<?php
require_once('smarty/Smarty.class.php');

class TemplateModel
{
	const TEMPLATE_SUFFIX = '.tpl';
	
	private $smarty;
	private $templateName;
	
	function __construct() {
		$this->smarty = new Smarty();
	}
	
	function setForHtml() {
		$this->smarty->default_modifiers = array('escape:"html"');
	}
	
	function setTemplateDir($templateDir, $compileDir = NULL) {
		$this->smarty->template_dir = $templateDir;
		$this->smarty->compile_dir = is_null($compileDir) ? ($templateDir  . '_c') : $compileDir;
	}
	
	function put($name, $value) {
		$this->smarty->assign_by_ref($name, $value);
	}
	
	function getValues() {
		return $this->smarty->get_template_vars();
	}
	
	function setTemplateName($templateName) {
		$this->templateName = $templateName;
	}
	
	/**
	 * Default: if SCRIPT_NAME is 'page-name.php' => 'page-name.tpl'
	 */
	function getTemplateFilePath() {
		if (!is_null($this->templateName))
			return $this->templateName . self::TEMPLATE_SUFFIX;
		return basename($_SERVER['SCRIPT_NAME'], ".php") . self::TEMPLATE_SUFFIX;
	}
	
	function render($templateFilePath = NULL) {
		$this->smarty->display(
			!is_null($templateFilePath) ? $templateFilePath : $this->getTemplateFilePath());
	}
	
	function renderAsString($templateFilePath = NULL) {
		return $this->smarty->fetch(
			!is_null($templateFilePath) ? $templateFilePath : $this->getTemplateFilePath());
	}
}
?>
