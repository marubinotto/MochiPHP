<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/db/Database.class.php');

abstract class ModelTestBase extends PHPUnit_Framework_TestCase
{
	static function createCleanMySQLDatabase() {
		$database = new Database('mysql', 'localhost', 'root', 'root', 'test');
		$database->dropAllTables();
		return $database;
	}
	
	static function avoidDuplicateADOdbLibInclude() {
		require_once('adodb5/adodb-lib.inc.php');
		global $ADODB_INCLUDED_LIB;
		$ADODB_INCLUDED_LIB = 1;
	}
	
	protected $database;	
	protected $repository;
	protected $object;
	
	function setUp() {
		self::avoidDuplicateADOdbLibInclude();
		$this->database = self::createCleanMySQLDatabase();
		$this->repository = $this->getRepository($this->database);
		PersistentObject::createTableFor(
			$this->repository->getObjectClassName(), $this->database);	
		$this->object = $this->repository->newInstance(); 
	}
	
	abstract function getRepository($database);
}
?>
