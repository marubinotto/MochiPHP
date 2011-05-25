<?php
require_once('mochi/Page.class.php');
require_once('mochi/controls/Form.class.php');
require_once('mochi/controls/TextField.class.php');
require_once('mochi/controls/RegexField.class.php');

class StatefulFormPage extends Page
{
	private $form;
	private $confirm;
	
	public $finished;
	
	function onPrepare(Context $context) {
		parent::onPrepare($context);
	
		// input form
		$this->form = new Form('form');
		$this->form->setStateful(true);		
		$this->form->addField(new TextField('username', 
			array(
				'size' => 40, 
				'maxLength' => 100, 
				'required' => true,
				'displayName' => 'Username')));
		$this->form->addField(new RegexField('email', 
			array(
				'size' => 50, 
				'maxLength' => 100, 
				'required' => true, 
				'pattern' => RegexField::PATTERN_MAIL,
				'displayName' => 'Email Address')));
		$this->addControl($this->form);
		
		// confirm 
		$this->confirm = new Form('confirm');
		$this->addControl($this->confirm);
		$this->confirm->setListenerOnValidSubmission($this->listenVia('onConfirm'));
	}
	
	function onConfirm($source, $context) {
		// some registration logic here
		
		$this->form->clearSessionState($context);
		$this->setRedirect($context->getResourcePath() . '?finished');
		return false;
	}
	
	function onRender(Context $context) {
		parent::onRender($context);
		$this->addModel('finished', !is_null($this->finished));
	}
}
?>
