<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/db/Database.class.php');
require_once('mochi/utils/StringUtils.class.php');
require_once('mochi/utils/ArrayUtils.class.php');

abstract class DatabaseTestBase extends PHPUnit_Framework_TestCase
{
	static function createCleanDatabase() {
		// $database = new Database('sqlitepo', ':memory:');
		// $database = new Database('mysql', 'localhost', 'root', 'root', 'test');
		$database = new Database('mysqlt', 'localhost', 'root', 'root', 'test');
		
		$database->dropAllTables();
		return $database;
	}
	
	static function createCleanMySQLDatabase() {
		// $database = new Database('mysql', 'localhost', 'root', 'root', 'test');
		$database = new Database('mysqlt', 'localhost', 'root', 'root', 'test');
		
		$database->dropAllTables();
		return $database;
	}
	
	/**
	 * Features that uses adodb-lib.inc.php will cause an error on PHPUnit:
	 * "Fatal error: Cannot redeclare adodb_strip_order_by()"
	 * 
	 * Because:
	 * - The global variable $ADODB_INCLUDED_LIB is reset by PHPUnit
	 *   even if adodb-lib.inc.php has been already included.
	 * - As a result, the duplication of adodb-lib.inc.php include is caused
	 */
	static function avoidDuplicateADOdbLibInclude() {
		require_once('adodb5/adodb-lib.inc.php');
		global $ADODB_INCLUDED_LIB;
		$ADODB_INCLUDED_LIB = 1;
	}
	
	const COMPATIBLE_SQL_TABLE ="
		create table test (
		  name varchar(100) not null,
		  value varchar(100),
		  primary key(name)
		)
	";
	
	protected $object;
	
	function setUp() {
		self::avoidDuplicateADOdbLibInclude();
		$this->object = self::createCleanDatabase();
	}
	
	protected function createTestTable() {
		$databaseType = $this->object->getADOConnection()->databaseType;
		if ($databaseType === 'mysqlt')
			$this->object->execute(self::COMPATIBLE_SQL_TABLE . ' TYPE=InnoDB');
		else
			$this->object->execute(self::COMPATIBLE_SQL_TABLE);
	}
	
	protected function selectAllRows() {
		return $this->object->queryForRows("select name, value from test");
	}
}

class DatabaseTest extends DatabaseTestBase
{
	function test_noTables() {
		$this->assertEquals(array(), $this->object->getTableNames());
	}
	
	function test_createTable() {
		$this->object->execute(self::COMPATIBLE_SQL_TABLE);
		$this->assertEquals(array("test"), $this->object->getTableNames());
	}
	
	function test_experiment() {
		// $this->assertEquals("", $this->object->getADOConnection()->databaseType);
		// $this->assertEquals("", $this->object->getDatabaseDescription() . " " . $this->object->getDatabaseVersion());
		// $this->assertEquals("", var_export($this->object->getADOConnection()->ServerInfo(), TRUE));
		// $this->assertEquals("", $this->object->getDataDictionary()->databaseType);
		// $this->assertEquals("", ArrayUtils::toString($this->object->getDatabaseVersionAsArray()));
	}
	
	/**
	 * IF EXISTS function, e.g. "DROP TABLE IF EXISTS temp;" Added in 3.3
	 * - http://www.mail-archive.com/sqlite-users@sqlite.org/msg19007.html
	 */
	function test_sqliteSupportsIfExists() {
		$this->assertTrue(Database::sqliteSupportsIfExists(array('3', '3')));
		$this->assertFalse(Database::sqliteSupportsIfExists(array('3', '2')));
		$this->assertFalse(Database::sqliteSupportsIfExists(array('2', '8')));
		$this->assertTrue(Database::sqliteSupportsIfExists(array('4', '0')));
	}
	
	function test_insertRowButNoTables() {
		$this->setExpectedException('DatabaseException');
		$this->object->insertRow("table", array("column" => "value"));
	}
	
	function test_nextSequenceValue() {
		$seqName = "test_sequence";
		$this->object->dropSequenceIfExists($seqName);
		
		$this->assertEquals(1, $this->object->nextSequenceValue($seqName));
		$this->assertEquals(2, $this->object->nextSequenceValue($seqName));
	}
	
	function test_escape() {
		$this->assertEquals("foo", $this->object->escape("foo"));
		$this->assertEquals("日本語", $this->object->escape("日本語"));
		// $this->assertEquals("", $this->object->escape("foo'bar"));
	}
}

class Database_EmptyTableTest extends DatabaseTestBase
{
	function setUp() {
		parent::setUp();
		$this->createTestTable();
	}
	
	function test_getColumnNames() {
		$this->assertEquals(
			array("name", "value"), 
			$this->object->getColumnNames("test"));
	}
		
	function test_update() {
		$affected = $this->object->update("insert into test values('user', 'akane')");
		$this->assertEquals(1, $affected);
	}
	
	function test_insertRow() {
		$this->object->insertRow("test", array("name" => "user", "value" => "akane"));
		
		$this->assertEquals(
			array(array("name" => "user", "value" => "akane")), 
			$this->selectAllRows());
	}
	
	function test_insertRowWithInvalidField() {
		$this->object->insertRow(
			"test", array("name" => "user", "value" => "akane", "foo" => "bar"));
			
		$this->assertEquals(
			array(array("name" => "user", "value" => "akane")), 
			$this->selectAllRows());
	}
	
	function test_insertRowWithSqlInjection() {
		$this->object->insertRow(
			"test", array("name" => "'', 'hogehoge'", "value" => "date('now')"));
		
		$this->assertEquals(
			array(array("name" => "'', 'hogehoge'", "value" => "date('now')")), 
			$this->selectAllRows());
	}
		
	function test_dropTable() {
		$this->object->dropTable("test");
		$this->assertEquals(array(), $this->object->getTableNames());
	}
	
	function test_dropTableIfExists() {
		$result = $this->object->dropTableIfExists("test");
		
		$this->assertTrue($result);
		$this->assertEquals(array(), $this->object->getTableNames());
	}
	
	function test_dropUnexistingTableIfExists() {
		$result = $this->object->dropTableIfExists("no-such-table");
		
		$this->assertFalse($result);
		$this->assertEquals(array("test"), $this->object->getTableNames());
	}
	
	function test_dropAllTables() {
		$dropped = $this->object->dropAllTables();
		
		$this->assertEquals(array("test"), $dropped);
		$this->assertEquals(array(), $this->object->getTableNames());
	}
}

class AutoNumberingIdTest extends DatabaseTestBase
{
	const TABLE_SQLITE = "
		create table book (
			id integer primary key, 
		  	title varchar not null
		)";
	const TABLE_MYSQL = "
		create table book (
			id integer not null auto_increment, 
		  	title varchar(100) not null,
		  	primary key(id)
		) TYPE=InnoDB";
	
	static $DRIVER_TO_SQL = array(
		'sqlitepo' => self::TABLE_SQLITE,
		'mysql' => self::TABLE_MYSQL,
		'mysqlt' => self::TABLE_MYSQL
	);
	
	function setUp() {
		parent::setUp();
		
		$databaseType = $this->object->getADOConnection()->databaseType;
		$this->object->execute(self::$DRIVER_TO_SQL[$databaseType]);
	}
	
	function test_insertRowReturnsId() {
		$id1 = $this->object->insertRow("book", array("title" => "Norwegian Wood"));
		$id2 = $this->object->insertRow("book", array("title" => "The Wind-up Bird Chronicle"));
		
		$this->assertEquals(
			array(
				array("id" => $id1, "title" => "Norwegian Wood"),
				array("id" => $id2, "title" => "The Wind-up Bird Chronicle")), 
			$this->object->queryForRows("select id, title from book"));
	}
}

class TransactionTest extends DatabaseTestBase
{
	function setUp() {
		parent::setUp();		
		$this->createTestTable();
	}
	
	function test_transaction_commit() {
		$this->object->beginTransaction();
		$this->object->insertRow("test", array("name" => "user", "value" => "akane"));
		$this->object->commit();
		
		$this->assertEquals(
			array(array("name" => "user", "value" => "akane")), 
			$this->selectAllRows());
	}
		
	function test_transaction_rollback() {
		$this->object->beginTransaction();
		$this->object->insertRow("test", array("name" => "user", "value" => "akane"));
		$this->object->rollback();
		
		$this->assertEquals(array(), $this->selectAllRows());
	}
}

class Database_OneRowTest extends DatabaseTestBase
{
	function setUp() {
		parent::setUp();
		$this->createTestTable();
		$this->object->update("insert into test values('user', 'akane')");
	}
	
	function test_queryForADORecordSet() {
		$rs = $this->object->queryForADORecordSet("select * from test");
		$rows = $rs->GetRows();
		
		$this->assertEquals(1, count($rows));
		$this->assertEquals("user", $rows[0]["name"]);
		$this->assertEquals("akane", $rows[0]["value"]);
	}
	
	function test_queryForRow() {
		$row = $this->object->queryForRow(
			"select * from test where name = ?", array("user"));
		$this->assertEquals("akane", $row["value"]);
	}
	
	function test_queryForUnexistingRow() {
		$row = $this->object->queryForRow(
			"select * from test where name = ?", array("password"));
		$this->assertNull($row);
	}
	
	function test_queryForValue() {
		$value = $this->object->queryForValue("select count(*) from test");
		$this->assertEquals(1, $value);
	}
	
	function test_queryForUnexistingValue() {
		$value = $this->object->queryForValue(
			"select value from test where name = ?", array("password"));
		$this->assertNull($value);
	}
	
	function test_queryForValueZero() {
		$value = $this->object->queryForValue(
			"select count(*) from test where name = ?", array("password"));
		$this->assertEquals(0, $value);
	}
	
	function test_sqlInjection() {
		$row = $this->object->queryForRow(
			"select * from test where name = ?", array("'' or 0 = 0"));
		$this->assertNull($row);
	}
	
	function test_updateRows() {
		$affected = $this->object->updateRows(
			"test", array("value" => "Daisuke"), "name = 'user'");
		
		$this->assertEquals(1, $affected);
		$this->assertEquals(
			"Daisuke", $this->object->queryForValue("select value from test"));
	}
	
	function test_updateRowsWithNull() {
		$affected = $this->object->updateRows(
			"test", array("value" => NULL), "name = 'user'");
		
		$this->assertEquals(1, $affected);
		$this->assertNull($this->object->queryForValue("select value from test"));
	}
	
	function test_updateRowsWithSqlInjection() {
		$affected = $this->object->updateRows(
			"test", array("value" => "date('now')"), "name = 'user'");
		
		$this->assertEquals(1, $affected);
		$this->assertEquals(
			"date('now')", $this->object->queryForValue("select value from test"));
	}
	
	function test_violateUnique() {
		$this->setExpectedException('DbConstraintViolationException');
		$this->object->update("insert into test values('user', 'akane')");
	}
}

class Database_TwoRowsTest extends DatabaseTestBase
{
	function setUp() {
		parent::setUp();
		$this->createTestTable();
		$this->object->update("insert into test values('user', 'akane')");
		$this->object->update("insert into test values('title', 'diary')");
	}
	
	function test_queryForValues() {
		$values = $this->object->queryForValues("select name from test order by name");
		$this->assertEquals(array("title", "user"), $values);
	}
	
	function test_queryForUnexistingValues() {
		$values = $this->object->queryForValues(
			"select name from test where name = ?", array("password"));
		$this->assertEquals(array(), $values);
	}
	
	function test_queryForRows() {
		$rows = $this->object->queryForRows("select name, value from test order by name");
		$this->assertEquals(
			array(
				array("name" => "title", "value" => "diary"),
				array("name" => "user", "value" => "akane")), 
			$rows);
	}
	
	function test_queryForUnexistingRows() {
		$rows = $this->object->queryForRows(
			"select * from test where name = ?", array("password"));
		$this->assertEquals(array(), $rows);
	}
	
	function test_createLimitClause() {
		$limit = $this->object->createLimitClause(1, 1);
		$values = $this->object->queryForValues("select name from test order by name". $limit);
		
		$this->assertEquals(array("user"), $values);
	}
}
?>
