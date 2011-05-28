<?php
require_once('mochi/Page.class.php');
require_once('_controls.php');

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
		
		// Redirect After Post pattern
		$this->setRedirectToSelf($context);
		return false;
	}
	
	function onCancelClick($source, $context) {
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
?>
