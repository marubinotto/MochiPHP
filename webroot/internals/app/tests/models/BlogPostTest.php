<?php
require_once('PHPUnit/Framework.php');
require_once('ModelTestBase.php');
require_once(dirname(__FILE__) . '/../../models/BlogPost.class.php');

class BlogPostTest extends ModelTestBase
{
	function getRepository($database) {
		return new BlogPostRepository($database);
	}
	
	function setUp() {
		parent::setUp();
	}
	
	function test_persistentFieldMappings() {
		$this->object->testPersistentFieldMappings();
	}
	
	function test_properties() {
		$this->object->title = 'Hello';
		$this->assertEquals('Hello', $this->object->title);
		
		$instance = new BlogPost();
		$instance->title = 'Hello';
		$this->assertEquals('Hello', $instance->title);
	}
	
	function test_CRUD() {
		// Create
		$instance = $this->repository->newInstance();
		$instance->title = 'MochiPHP';
		$instance->content = 'MochiPHP is a lightweight framework for PHP.';
		$now = $instance->formatTimestamp();
		$instance->registerDatetime = $now;
		$instance->updateDatetime = $now;
		$instance->save();
		$id = $instance->id;
		
		$this->assertEquals(1, $this->repository->count());
		
		// Read
		$instance = $this->repository->findById($id);
		$this->assertEquals('MochiPHP', $instance->title);
		$this->assertEquals('MochiPHP is a lightweight framework for PHP.', $instance->content);
		
		// Update
		$instance->title = "What is MochiPHP?";
		$instance->updateDatetime = $instance->formatTimestamp();
		$instance->save();
		
		$instance = $this->repository->findById($id);
		$this->assertEquals('What is MochiPHP?', $instance->title);
		
		// Delete
		$this->repository->deleteById($id);
		$this->assertEquals(0, $this->repository->count());
	}
}
?>
