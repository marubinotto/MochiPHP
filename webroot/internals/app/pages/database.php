<?php
require_once('mochi/Page.class.php');
require_once('mochi/EventListener.class.php');
require_once('mochi/controls/Form.class.php');
require_once('mochi/db/Database.class.php');
require_once('mochi/db/PersistentObject.class.php');
require_once('mochi/db/PersistentObjectRepository.class.php');

class DatabasePage extends Page
{
	private $database;
	
	private $createTables;
	private $createTable;
	
	public $class;
	
	function __construct() {
		parent::__construct();
		
		$this->createTables = new Form("createTables");
		$this->addControl($this->createTables);
		$this->createTables->setListenerOnValidSubmission(
			new EventListener($this, "onCreateTables"));
			
		$this->createTable = new Form("createTable");		
		$this->addControl($this->createTable);
		$this->createTable->setListenerOnValidSubmission(
			new EventListener($this, "onCreateTable"));
	}
	
	function onPrepare(Context $context) {
		parent::onPrepare($context);
		
		$this->database = $this->getFactory()->getDatabase();
		$this->createTable->addHiddenValue("class", $this->class);
	}
	
	function onCreateTables($form, $context) {
		$tableDefs = PersistentObject::createTableForEach(
			$this->getFactory()->getPersistentObjectClasses(), 
			$this->database,
			TRUE);	// replace
		$this->addModel('tableDefs', $tableDefs);
		return TRUE; 
	}
	
	function onCreateTable($form, $context) {
		if (!is_null($this->class)) {
			$tableDefs = PersistentObject::createTableFor($this->class, $this->database, TRUE);
			$this->addModel('tableDefs', $tableDefs);
		}		
		return TRUE;
	}
	
	function onRender(Context $context) {
		parent::onRender($context);
		
		$this->addModel('phpVersion', PHP_VERSION);
		$this->addModel('database', $this->database);
		$this->addModel('classes', $this->getFactory()->getPersistentObjectClasses());
		
		if (!is_null($this->class)) {
			$this->addModel('selectedClass', $this->class);
			
			$instance = new $this->class();
			$this->addModel('instance', $instance);
			
			$repository = new InstantRepository($this->class, $this->database);
			$tableName = $repository->getTableName();
			$this->addModel('tableName', $tableName);
			
			$tableExists = in_array($tableName, $this->database->getTableNames());
			$this->addModel('tableExists', $tableExists);
			if (!$tableExists) return;
			
			$columnNames = $this->database->getColumnNames($tableName);
			$this->addModel('columnNames', $columnNames);
			
			if ($instance->supportsPrivateAccess())
				$this->addModel('rows', $repository->findAll());
		}
	}
}
?>
