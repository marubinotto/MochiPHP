<?php
require_once('mochi/Page.class.php');
require_once('_controls.php');

class PostPage extends Page
{
	public $id;
	public $edit;
	
	private $post;
	
	private $form;
	
	function onPermissionCheck(Context $context) {
		if (!parent::onPermissionCheck($context)) return false;
	
		if (!is_null($this->id)) {
			$repository = $this->getFactory()->getBlogPostRepository();
			$this->post = $repository->findById($this->id);
		}
		if (is_null($this->post)) {
			$this->setRedirect('/blog/index');
			return false;
		}
		return true;
	}
	
	function onPrepare(Context $context) {
		parent::onPrepare($context);
	
		$this->form = new BlogPostForm('form', $this);
		$this->form->copyValuesFrom($this->post);
		$this->form->addHiddenValue('id', $this->post->id);
		$this->addControl($this->form);
	}
	
	function onPreviewClick($source, $context) {
		$this->addModel('preview', true);
		return true;
	}
	
	function onPostClick($source, $context) {
		if (!$this->form->isValid()) return true;
		
		$this->form->copyValuesTo($this->post);
		$this->post->updateDatetime = $this->post->formatTimestamp();
		$this->post->save();
		
		$this->setRedirectToSelf($context);
		return true;
	}
	
	function onCancelClick($source, $context) {
		$this->setRedirectToSelf($context);
		return false;
	}
	
	function setRedirectToSelf(Context $context) {
		$this->setRedirect($context->getResourcePath() . "?id={$this->post->id}");
	}
	
	function onRender(Context $context) {
		parent::onRender($context);
		
		$this->addModel('post', $this->post);
		$this->addModel('edit', 
			!is_null($this->edit) || $this->form->isSubmitted());
	}
}
?>
