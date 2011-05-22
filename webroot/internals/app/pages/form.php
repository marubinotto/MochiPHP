<?php
require_once('mochi/Page.class.php');
require_once('mochi/controls/Form.class.php');
require_once('mochi/controls/TextArea.class.php');

class FormPage extends Page
{
	private $form;
	private $entries;
	
	function onPrepare(Context $context) {
		parent::onPrepare($context);
	
		// Form
		$this->form = new Form('form');
		$this->form->addField(new TextArea('content', 
			array("cols" => 50, "rows" => 3, "required" => true)));
		$this->form->setListenerOnValidSubmission($this->listenVia('onSubmit'));
		$this->addControl($this->form);
		
		// Data store
		$this->entries = $context->getSession()->get('entries');
		if (!is_array($this->entries)) $this->entries = array();
	}
	
	function onSubmit($source, Context $context) {
		// Store the sent data 
		array_unshift($this->entries, $this->form->getValue('content'));
		$context->getSession()->set('entries', $this->entries);
		
		// Redirect After Post
		$this->setRedirectToSelf($context);
		return false;
	}
	
	function onRender(Context $context) {
		parent::onRender($context);
		$this->addModel('entries', $this->entries);
	}
}
?>
