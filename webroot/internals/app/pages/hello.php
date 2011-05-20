<?php
require_once('mochi/Page.class.php');

class HelloPage extends Page
{
	public $name;
	
	function onRender(Context $context) {
		parent::onRender($context);
		
		$name = is_null($this->name) ? 'world' : $this->name;
		$this->addModel('message', "Hello, {$name}!");
	}
}
?>
