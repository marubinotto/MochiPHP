<?php
require_once('mochi/Page.class.php');
require_once('mochi/controls/Form.class.php');
require_once('mochi/controls/TextField.class.php');
require_once('mochi/controls/TextArea.class.php');

class IndexPage extends Page
{
	private $form;
	
	function onPrepare(Context $context) {
		parent::onPrepare($context);
	
		$this->form = new BlogPostForm('form');
		$this->addControl($this->form);
	}
	
	function onRender(Context $context) {
		parent::onRender($context);

	}
}

class BlogPostForm extends Form
{
	function __construct($name) {
		parent::__construct($name);
		
		$this->setStateful(TRUE);
		
		$this->addField(new TextField('title', 
			array("size" => 60, 'maxLength' => 100, "required" => FALSE)));
		$this->addField(new TextArea('content', 
			array("cols" => 50, "rows" => 6, "required" => FALSE)));
	}
}
?>
