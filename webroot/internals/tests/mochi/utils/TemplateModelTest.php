<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/utils/SmartyUtils.class.php');

class TemplateModelTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		$this->object = new TemplateModel();
	}
	
	function test_templateFilePathFromName() {
		$this->object->setTemplateName("page-path");
		$this->assertEquals("page-path.tpl", $this->object->getTemplateFilePath());
	}
}
?>
