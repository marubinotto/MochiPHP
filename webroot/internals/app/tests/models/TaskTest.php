<?php
require_once('PHPUnit/Framework.php');
require_once('ModelTestBase.php');
require_once(dirname(__FILE__) . '/../../models/Task.class.php');

class TaskTest extends ModelTestBase
{
	function getRepository($database) {
		return new TaskRepository($database);
	}
	
	function setUp() {
		parent::setUp();
	}
	
	function test_persistentFieldMappings() {
		$this->object->testPersistentFieldMappings();
	}
}
?>
