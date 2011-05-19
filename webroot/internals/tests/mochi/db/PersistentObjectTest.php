<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/db/PersistentObject.class.php');
require_once('DatabaseTest.php');

abstract class PersistentObjectWithDatabaseTestBase extends PHPUnit_Framework_TestCase
{
	protected $database;
	
	function setUp() {
		DatabaseTestBase::avoidDuplicateADOdbLibInclude();
		$this->database = DatabaseTestBase::createCleanDatabase();
	}
}

class ObjectWithSqlDdl
{
	const TABLE_NAME = "test";
	const TABLE_DEF = DatabaseTestBase::COMPATIBLE_SQL_TABLE;
}

class ObjectWithADOdbDdl
{
	const TABLE_NAME = "user";
	
	// Definition with ADOdb Data Dictionary
	// http://phplens.com/lens/adodb/docs-datadict.htm
	const TABLE_DEF = "
		id I NOTNULL AUTOINCREMENT PRIMARY,
		name C(100) NOTNULL,
		display_name C(100) NOTNULL
	";	// TODO how can I set a UNIQUE constraint?
}

class CreatePersistentObjectTableTest extends PersistentObjectWithDatabaseTestBase
{
	function test_createTableWithSqlDdl() {
		$sqls = PersistentObject::createTableFor("ObjectWithSqlDdl", $this->database);
		
		$this->assertEquals(array(ObjectWithSqlDdl::TABLE_DEF), $sqls);
		$this->assertEquals(array("test"), $this->database->getTableNames());
	}
	
	function test_createTableWithADOdbDdl() {
		$sqls = PersistentObject::createTableFor("ObjectWithADOdbDdl", $this->database);
		
		$this->assertTrue(is_array($sqls));
		// $this->assertEquals(array(), $sqls);
		$this->assertEquals(array("user"), $this->database->getTableNames());
	}
	
	function test_createDuplicateTable() {
		PersistentObject::createTableFor("ObjectWithSqlDdl", $this->database);
		$this->insertOneRowToTestTable();
		$this->assertEquals(1, $this->countRowsInTestTable());

		// Create a duplicate table
		$sqls = PersistentObject::createTableFor("ObjectWithSqlDdl", $this->database);
		
		$this->assertNull($sqls);
		$this->assertEquals(array("test"), $this->database->getTableNames());
		$this->assertEquals(1, $this->countRowsInTestTable());
	}
	
	function test_replaceTable() {
		// Old table with one row
		PersistentObject::createTableFor("ObjectWithSqlDdl", $this->database);
		$this->insertOneRowToTestTable();
		$this->assertEquals(1, $this->countRowsInTestTable());
		
		// Replace the table
		$sqls = PersistentObject::createTableFor("ObjectWithSqlDdl", $this->database, TRUE);
		
		$this->assertEquals(array(ObjectWithSqlDdl::TABLE_DEF), $sqls);
		$this->assertEquals(array("test"), $this->database->getTableNames());
		$this->assertEquals(0, $this->countRowsInTestTable());
	}
	
	private function insertOneRowToTestTable() {
		$this->database->insertRow(
			ObjectWithSqlDdl::TABLE_NAME, 
			array("id" => 1, "name" => "akane", "display_name" => "Akane"));
	}
	
	private function countRowsInTestTable() {
		return $this->database->queryForValue("select count(*) from test");
	}
}

class PersistentArticle extends PersistentObject
{
	const TABLE_NAME = "article";
  
  	// Definition with SQL table DDL (MySQL)
	const TABLE_DEF = "
		create table %s (
			id integer not null auto_increment, 
			title varchar(100),
			content text,
			update_datetime datetime not null,
			primary key(id)
	    );
	";
	
	private $title;
	private $content;
	private $updateDatetime;
	
	function getTitle() {
		return $this->title;
	}
	  
	function setTitle($title) {
		$this->title = $title;
	}
	
	function getContent() {
		return $this->content;
	}
	  
	function setContent($content) {
		$this->content = $content;
	}
	
	function getUpdateDatetime() {
		return $this->updateDatetime;
	}
	  
	function setUpdateDatetime($updateDatetime) {
		$this->updateDatetime = $updateDatetime;
	}
}

class PersistentObjectTest extends PHPUnit_Framework_TestCase
{
	protected $object;
	
	function setUp() {
		$this->object = new PersistentArticle();
	}
	
	function test_emptyObjectToDatabaseRow() {
		$row = $this->object->toDatabaseRow();
		
		$this->assertEquals(
			array(
				"id" => NULL, 
				"title" => NULL, 
				"content" => NULL,
				"update_datetime" => NULL), 
			$row);
	}
	
	function test_toDatabaseRow() {
		$this->object->setTitle("Akaen Was Born");
	    $this->object->setContent("What a great day!");  
	    $this->object->setUpdateDatetime("1971-06-14 12:23:45");
	    
		$row = $this->object->toDatabaseRow();
		
		$this->assertEquals(
			array(
				"id" => NULL, 
				"title" => "Akaen Was Born", 
				"content" => "What a great day!",
				"update_datetime" => "1971-06-14 12:23:45"), 
			$row);
	}
	
	function test_bindDatabaseRow() {
		$this->object->bindDatabaseRow(array(
			"id" => 1, 
			"title" => "Akaen Was Born", 
			"content" => "What a great day!",
			"update_datetime" => "1971-06-14 12:23:45"
		));
		
		$this->assertEquals(1, $this->object->getId());
		$this->assertEquals("Akaen Was Born", $this->object->getTitle());
		$this->assertEquals("What a great day!", $this->object->getContent());
		$this->assertEquals("1971-06-14 12:23:45", $this->object->getUpdateDatetime());
	}
}

class ValidMappingObject extends PersistentObject
{
	const TABLE_NAME = "article";
	
	private $p_title;
	private $p_content;
	private $p_update_datetime;
}

class InvalidMappingObject extends PersistentObject
{
	const TABLE_NAME = "article";
	
	private $p_title;
	private $p_content;
	private $p_update_datetime;
	private $p_hogehoge;
}

class OnePersistentObjectTest extends PersistentObjectWithDatabaseTestBase
{
	private $article;
	
	function setUp() {
		parent::setUp();
		
		$this->database = DatabaseTestBase::createCleanMySQLDatabase();
		
		// Create a table
		PersistentObject::createTableFor("PersistentArticle", $this->database, TRUE);
		
		// Insert a row
		$article = new PersistentArticle();
		$article->setDatabase($this->database);
	    $article->setTitle("Akaen Was Born");
	    $article->setContent("What a great day!");  
	    $article->setUpdateDatetime("1971-06-14 12:23:45");
	    $article->save();
	    $this->article = $article;
	}
	
	private function selectAllRows() {
		return $this->database->queryForRows("select * from article");
	}
	
	function test_testPersistentFieldMappings() {
		$object = new ValidMappingObject();
		$object->setDatabase($this->database);
		
		$object->testPersistentFieldMappings();
	}
	
	function test_testPersistentFieldMappingError() {
		$object = new InvalidMappingObject();
		$object->setDatabase($this->database);
		
		$this->setExpectedException('Exception');
		$object->testPersistentFieldMappings();
	}
	
	function test_savedNewInstance() {
	    $this->assertEquals(
			array(array(
				"id" => $this->article->getId(),
				"title" => "Akaen Was Born",
				"content" => "What a great day!",
				"update_datetime" => "1971-06-14 12:23:45"
			)), 
			$this->selectAllRows());
	}
	
	function test_saveUpdatedInstance() {
		$this->article->setContent("What a miracle day!!");
		$this->article->save();
		
	    $this->assertEquals(
			array(array(
				"id" => $this->article->getId(),
				"title" => "Akaen Was Born",
				"content" => "What a miracle day!!",
				"update_datetime" => "1971-06-14 12:23:45"
			)), 
			$this->selectAllRows());
	}
}

class TwoPersistentObjectsTest extends PersistentObjectWithDatabaseTestBase
{
	private $article1;
	private $article2;
	
	function setUp() {
		parent::setUp();
		
		$this->database = DatabaseTestBase::createCleanMySQLDatabase();
		
		// Create a table
		PersistentObject::createTableFor("PersistentArticle", $this->database, TRUE);
		
		// Insert two rows
		$article = new PersistentArticle();
	    $article->setDatabase($this->database);  
	    $article->setTitle("title1");
	    $article->setUpdateDatetime("2011-02-14 00:00:00");  
	    $article->save();
	    $this->article1 = $article;
	    
	    $article = new PersistentArticle();
	    $article->setDatabase($this->database);  
	    $article->setTitle("title2");
	    $article->setUpdateDatetime("2011-02-15 00:00:00");  
	    $article->save();
	    $this->article2 = $article;
	}
	
	private function selectAllRows() {
		return $this->database->queryForRows("select * from article");
	}
	
	function test_savedNewInstances() {
	    $this->assertEquals(
			array(
				array(
					"id" => $this->article1->getId(),
					"title" => "title1",
					"content" => NULL,
					"update_datetime" => "2011-02-14 00:00:00"),
				array(
					"id" => $this->article2->getId(),
					"title" => "title2",
					"content" => NULL,
					"update_datetime" => "2011-02-15 00:00:00")
				), 
			$this->selectAllRows());
	}
		
	function test_saveUpdatedInstance() {
		$this->article1->setContent("content1");
		$this->article1->setUpdateDatetime("2011-02-16 00:00:00");
		$this->article1->save();
		
	    $this->assertEquals(
			array(
				array(
					"id" => $this->article1->getId(),
					"title" => "title1",
					"content" => "content1",											// updated
					"update_datetime" => "2011-02-16 00:00:00"),	// updated
				array(
					"id" => $this->article2->getId(),
					"title" => "title2",
					"content" => NULL,
					"update_datetime" => "2011-02-15 00:00:00")
				), 
			$this->selectAllRows());
	}
}
?>
