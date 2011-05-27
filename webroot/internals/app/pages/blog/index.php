<?php
require_once('mochi/Page.class.php');
require_once('mochi/controls/Form.class.php');
require_once('mochi/controls/TextField.class.php');
require_once('mochi/controls/TextArea.class.php');
require_once('mochi/controls/Submit.class.php');

class IndexPage extends Page
{
	const PAGE_SIZE = 10;
	public $page = 0;
	
	private $form;
	
	function onPrepare(Context $context) {
		parent::onPrepare($context);
	
		$this->form = new BlogPostForm('form', $this);
		$this->addControl($this->form);
	}
	
	function onPreviewClick($source, $context) {
		$this->addModel('preview', true);
		return true;
	}
	
	function onPostClick($source, $context) {
		if (!$this->form->isValid()) return true;
		
		// Store a new post to the database
		$repository = $this->getFactory()->getBlogPostRepository();
		$instance = $repository->newInstance();		
		$this->form->copyValuesTo($instance);
		$now = $instance->formatTimestamp();
		$instance->registerDatetime = $now;
		$instance->updateDatetime = $now;
		$instance->save();
		
		$this->form->clearSessionState($context);
		
		// Redirect After Post pattern
		$this->setRedirectToSelf($context);
		return false;
	}
	
	function onCancelClick($source, $context) {
		$this->form->clearSessionState($context);
		$this->setRedirectToSelf($context);
		return false;
	}
	
	function onRender(Context $context) {
		parent::onRender($context);

		$repository = $this->getFactory()->getBlogPostRepository();
		$pagination = array('size' => self::PAGE_SIZE, 'index' => $this->page);
		$posts = $repository->getRecentlyRegistered($pagination);
		$this->addModel('posts', $posts);
	}
}

class BlogPostForm extends Form
{
	function __construct($name, $page) {
		parent::__construct($name);
		
		$this->setStateful(true);
		
		$this->addField(new TextField('title', 
			array("size" => 60, 'maxLength' => 100, "required" => false)));
		$this->addField(new TextArea('content', 
			array("cols" => 50, "rows" => 6, "required" => false)));
			
		$this->addField(new Submit('preview',
			$page->listenVia('onPreviewClick'), array("displayName" => "Preview")));
		$this->addField(new Submit('post',
			$page->listenVia('onPostClick'), array("displayName" => "Post")));
		$this->addField(new Submit('cancel',
			$page->listenVia('onCancelClick'), array("displayName" => "Cancel")));
	}
}
?>
