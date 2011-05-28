<?php
require_once('mochi/Page.class.php');

class PostPage extends Page
{
	public $id;
	private $post;
	
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
	
	function onRender(Context $context) {
		parent::onRender($context);
		
		$this->addModel('post', $this->post);
	}
}
?>
