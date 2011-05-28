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
	
	function __construct(AppResources $appResources) {
		$this->appResources = $appResources;
		$this->settings = $this->appResources->getSettings();
	}
	
	function getSettings() { 
		return $this->settings; 
	}

	private $database;
	
	function getDatabase() {
		if (is_null($this->database)) {
			$this->database = new Database(
				$this->settings->get('database.driver'), 
				$this->settings->get('database.host'), 
				$this->settings->get('database.user'), 
				$this->settings->get('database.password'), 
				$this->settings->get('database.database'));
		}
		return $this->database;
	}
	
	function getPersistentObjectClasses() {
		return array('BlogPost');
	}
	
	function getBlogPostRepository() {
		return new BlogPostRepository($this->getDatabase());
	}
}
?>
