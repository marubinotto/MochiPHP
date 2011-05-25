<?php
require_once('mochi/Page.class.php');

class IndexPage extends Page
{
	function onRender(Context $context) {
		parent::onRender($context);
		
		$this->addModel('version', Context::MOCHI_VERSION);
	}
}
?>
