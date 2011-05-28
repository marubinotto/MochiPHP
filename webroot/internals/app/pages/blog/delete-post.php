<?php
require_once('mochi/Page.class.php');

class DeletePostPage extends Page
{
	public $id;
	
	function processRequest(Context $context) {
		if (!is_null($this->id)) {
			$repository = $this->getFactory()->getBlogPostRepository();
			$repository->deleteById($this->id);
		}
		return false;
	}
}
?>
