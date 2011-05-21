<?php
require_once('mochi/AppResources.class.php');
require_once('mochi/db/Database.class.php');
require_once('mochi/db/PersistentObject.class.php');
require_once(dirname(__FILE__) . '/../models/BlogPost.class.php');
require_once(dirname(__FILE__) . '/../models/Task.class.php');

class Factory
{
	private $appResources;
	private $settings;
	
	function __construct($appResources) {
		$this->appResources = $appResources;
		$this->settings = require('settings.php');
	}
	
	function getSettings() { 
		return $this->settings; 
	}

	private $database;
	
	function getDatabase() {
		if (is_null($this->database)) {
			$this->database = new Database(
				$this->settings['database.driver'], 
				$this->settings['database.host'], 
				$this->settings['database.user'], 
				$this->settings['database.password'], 
				$this->settings['database.database']);
		}
		return $this->database;
	}
	
	function getPersistentObjectClasses() {
		return array('BlogPost', 'Task');
	}
	
	function getBlogPostRepository() {
		return new BlogPostRepository($this->getDatabase());
	}
	
	function getTaskRepository() {
		return new TaskRepository($this->getDatabase());
	}
}
?>
