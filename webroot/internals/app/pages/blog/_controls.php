<?php
require_once('mochi/controls/Form.class.php');
require_once('mochi/controls/TextField.class.php');
require_once('mochi/controls/TextArea.class.php');
require_once('mochi/controls/Submit.class.php');

class BlogPostForm extends Form
{
	function __construct($name, $page) {
		parent::__construct($name);
		
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
