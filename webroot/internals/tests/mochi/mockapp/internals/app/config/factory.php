<?php
require_once('mochi/AppResources.class.php');
require_once(dirname(__FILE__) . '/../models/PersonRepository.class.php');

class Factory
{
	public $appResources;
	
	function __construct($appResources) {
		$this->appResources = $appResources;
	}
	
	function getPersonRepository() {
		return new PersonRepository();
	}
}
?>
