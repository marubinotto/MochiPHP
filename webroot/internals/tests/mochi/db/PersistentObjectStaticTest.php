<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/db/PersistentObject.class.php');

class ObjectWithTableDef
{
	const TABLE_NAME = "table_name";
	const TABLE_DEF = "create table %s (...)";
}

class PersistentObjectStaticTest extends PHPUnit_Framework_TestCase
{
	// camelCaseToDbIdentifier
	
	function test_camelCaseToDbIdentifier() {
		$this->assertNull(PersistentObject::camelCaseToDbIdentifier(NULL));
		$this->assertEquals("object", PersistentObject::camelCaseToDbIdentifier("Object"));
		$this->assertEquals("persistent_object", PersistentObject::camelCaseToDbIdentifier("PersistentObject"));
		$this->assertEquals("display_name", PersistentObject::camelCaseToDbIdentifier("displayName"));
	}
	
	// dbIdentifierToCamelCase
	
	function test_dbIdentifierToCamelCase() {
		$this->assertNull(PersistentObject::dbIdentifierToCamelCase(NULL));
		$this->assertEquals("id", PersistentObject::dbIdentifierToCamelCase("id"));
		$this->assertEquals("userId", PersistentObject::dbIdentifierToCamelCase("user_id"));
		$this->assertEquals("eventFileName", PersistentObject::dbIdentifierToCamelCase("event_file_name"));
	}
	
	// persistent field name
	
	function test_camelCaseToPersistentFieldName() {
		$this->assertEquals(
			"p_camel_case", PersistentObject::camelCaseToPersistentFieldName("camelCase"));
	}
	
	function test_persistentFieldNameToCamelCase() {
		$this->assertEquals(
			"updateDatetime", PersistentObject::persistentFieldNameToCamelCase("p_update_datetime"));
		$this->assertNull(PersistentObject::persistentFieldNameToCamelCase("update_datetime"));
	}
	
	// getTableNameFor
	
	function test_getTableNameByConstant() {
		$this->assertEquals("table_name", PersistentObject::getTableNameFor("ObjectWithTableDef"));
	}
	
	function test_getDefaultTableName() {
		$this->assertEquals("object", PersistentObject::getTableNameFor("Object"));
	}
	
	function test_getDefaultTableNameWithTwoWords() {
		$this->assertEquals("persistent_object", PersistentObject::getTableNameFor("PersistentObject"));
	}
	
	// getTableDefFor
	
	function test_getTableDef() {
		$this->assertEquals(
			"create table table_name (...)", 
			PersistentObject::getTableDefFor("ObjectWithTableDef"));
	}
	
	function test_getUnexistingTableDef() {
		$this->assertNull(PersistentObject::getTableDefFor("Object"));
	}
}
?>
