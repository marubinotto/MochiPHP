<?php
require_once(dirname(__FILE__) . '/../utils/Object.class.php');
require_once(dirname(__FILE__) . '/../utils/StringUtils.class.php');
require_once(dirname(__FILE__) . '/../utils/Timestamp.class.php');
require_once('Database.class.php');

abstract class PersistentObject extends Object
{
	const CONST_TABLE_NAME = 'TABLE_NAME';
	const CONST_TABLE_DEF = 'TABLE_DEF';
	const PERSISTENT_FIELD_PREFIX = 'p_';
	
	static function camelCaseToDbIdentifier($camelCase) {
		if (is_null($camelCase)) return NULL;
		$spaceInserted = preg_replace('/(?!^)[[:upper:]]/',' \0', $camelCase);
		$identifier = '';
		foreach (StringUtils::split($spaceInserted, ' ') as $token) {
			if (StringUtils::length($identifier) > 0) $identifier .= '_';
			$identifier .= strtolower($token);
		}
		return $identifier;
	}
	
	static function dbIdentifierToCamelCase($identifier) {
		if (is_null($identifier)) return NULL;
		$camelCase = '';
		foreach (StringUtils::split($identifier, '_') as $index => $token) {
			if ($index > 0) $token = StringUtils::toUpperCaseFirstChar($token);
			$camelCase .= $token;
		}
		return $camelCase;
	}
	
	static function camelCaseToPersistentFieldName($camelCase) {
		$dbIdentifier = self::camelCaseToDbIdentifier($camelCase);
		return self::PERSISTENT_FIELD_PREFIX . $dbIdentifier;
	}
	
	static function persistentFieldNameToCamelCase($fieldName) {
		if (!StringUtils::startsWith($fieldName, self::PERSISTENT_FIELD_PREFIX))
			return NULL;
		$dbIdentifier = StringUtils::removeStart($fieldName, self::PERSISTENT_FIELD_PREFIX);
		return self::dbIdentifierToCamelCase($dbIdentifier);
	}
	
	static function getTableNameFor($className) {
		$class = new ReflectionClass($className);
		$tableName = $class->getConstant(self::CONST_TABLE_NAME);
		return $tableName ? $tableName : 
			self::camelCaseToDbIdentifier($class->getName());
	}
	
	static function getTableDefFor($className) {
		$class = new ReflectionClass($className);	
		$tableDef = $class->getConstant(self::CONST_TABLE_DEF);
		if (!$tableDef) return NULL;
		return sprintf($tableDef, self::getTableNameFor($className));
	}
	
	static function createTableFor($className, Database $database, $replace = FALSE) {
		$tableDef = self::getTableDefFor($className);
		if (is_null($tableDef))
			throw new Exception('Table definition not found: ' + $className);
			
		$tableName = self::getTableNameFor($className);
		if ($replace) {
			$database->dropTableIfExists($tableName);
		}
		else {
			if (in_array($tableName, $database->getTableNames())) return NULL;
		}
		
		// Create with SQL table DDL
		if (StringUtils::startsWith(strtolower(trim($tableDef)), 'create ')) {
			$database->execute($tableDef);
			return array($tableDef);
		}
		
		// Create with ADOdb Data Dictionary Library for PHP
		// http://phplens.com/lens/adodb/docs-datadict.htm
		$sqls = $database->createTableSqlsFromADOdbDdl($tableName, $tableDef);
		foreach ($sqls as $sql) $database->execute($sql);
		return $sqls;
	}
	
	static function createTableForEach(array $classNames, Database $database, $replace = FALSE) {
		$tableDefs = array();
		foreach ($classNames as $className) {
			$sqls = self::createTableFor($className, $database, $replace);
			if (count($sqls) > 0) $tableDefs[] = $sqls[0];
		}
		return $tableDefs;
	}
	
	
	private $id = NULL;
	
	function getId() {
		return $this->id;
	}
	
	function setId($id) {
		$this->id = $id;
	}
	
	
	private $database;
	private $repository;

	final function setDatabase(Database $database) {
		$this->database = $database;
	}
	
	final function setRepository($repository) {
		$this->repository = $repository;
	}
	
	final function getRepository() {
		return $this->repository;
	}
	
	final function getTableName() {
		return self::getTableNameFor($this->getClassName());
	}
	
	final function getPersistentPropertyNames() {
		$names = array();
		foreach ($this->getClass()->getProperties() as $property) {
			$name = self::persistentFieldNameToCamelCase($property->getName());
			if (is_null($name)) continue;
			$names[] = $name;
		}
		return $names;
	}
	
	final function testPersistentFieldMappings() {
		$columnNames = $this->database->getColumnNames($this->getTableName());
		foreach ($this->getPersistentPropertyNames() as $propertyName) {
			$columnName = self::camelCaseToDbIdentifier($propertyName);
			if (!in_array($columnName, $columnNames)) {
				throw new Exception(
					"Missing expected column [{$columnName}] in table [{$this->getTableName()}]");
			}
		}
	}
	
	final function getPersistentPropertyValue($name) {
		$fieldName = self::camelCaseToPersistentFieldName($name);
		$property = $this->getClass()->getProperty($fieldName);
		$property->setAccessible(TRUE);
		return $property->getValue($this);
	}
	
	function __get($propertyName) {
		try {
			return parent::__get($propertyName);
		}
		catch (AccessorNotFoundException $e1) {
			try {
				return $this->getPersistentPropertyValue($propertyName);
			}
			catch (ReflectionException $e2) {
				throw $e1;
			}
		}
	}
	
	final function setPersistentPropertyValue($name, $value) {
		$fieldName = self::camelCaseToPersistentFieldName($name);
		try {
			$property = $this->getClass()->getProperty($fieldName);
		}
		catch (ReflectionException $e) {
			return FALSE;
		}
		
		$property->setAccessible(TRUE);
		$property->setValue($this, $value);
		return TRUE;
	}
	
	function __set($propertyName, $value) {
		try {
			parent::__set($propertyName, $value);
		}
		catch (AccessorNotFoundException $e) {
			if (!$this->setPersistentPropertyValue($propertyName, $value)) {
				throw $e;
			}
		}
	}
	
	static private $EXCLUDE_PROPERTIES = 
		array('repository', 'tableName', 'persistentPropertyNames');
		
	function toValues() {
		// getters
		$values = $this->getValuesViaGetters(self::$EXCLUDE_PROPERTIES);
		
		// or persistent properties
		foreach ($this->getPersistentPropertyNames() as $name) {
			if (!array_key_exists($name, $values)) {
				$values[$name] = $this->getPersistentPropertyValue($name);
			}
		}
		
		return $values;
	}
	
	function toDatabaseRow() {
		$row = array();
		foreach ($this->toValues() as $name => $value) {
			$row[self::camelCaseToDbIdentifier($name)] = $value;
		}
		return $row;
	}
	
	function bindValues($values) {
		foreach ($values as $name => $value) {
			if (!$this->setValueViaSetter($name, $value)) {		// setter
				$this->setPersistentPropertyValue($name, $value);	// or persistent property
			}
		}
	}
	
	function bindDatabaseRow($row) {
		foreach ($row as $name => $value) {
			$propertyName = self::dbIdentifierToCamelCase($name);
			if (!$this->setValueViaSetter($propertyName, $value)) {		// setter
				$this->setPersistentPropertyValue($propertyName, $value);	// or persistent property
			}
		}
	}
	
	function save() {
		assert('!is_null($this->database)');
		
		$row = $this->toDatabaseRow();
		if (is_null($this->getId())) {
			$newId = $this->database->insertRow($this->getTableName(), $row);
			$this->setId($newId);
		}
		else {
			assert('is_numeric($this->getId())');
			$this->database->updateRows(
				$this->getTableName(), $row, 'id = ' . $this->getId());
		}
	}
	
	function formatTimestamp() {
		return $this->database->formatTimestamp();
	}
	
	function time($propertyName, $format = NULL) {
		$value = $this->__get($propertyName);
		if (is_null($value)) return NULL;
		
		$timestamp = Timestamp::parse($value);
		
		if (is_null($format)) return $timestamp;
		return $timestamp->format($format);
	}
	
	function generateAccessorsCode() {
		$code = '';
		foreach ($this->getPersistentPropertyNames() as $name) {
			$capitalizedName = StringUtils::toUpperCaseFirstChar($name);
			$fieldName = self::camelCaseToPersistentFieldName($name);
			
			$code .= "function get{$capitalizedName}() {\n";
			$code .= "    return \$this->{$fieldName};\n";
			$code .= "}\n\n";
			
			$code .= "function set{$capitalizedName}(\${$name}) {\n";
			$code .= "    \$this->{$fieldName} = \${$name};\n";
			$code .= "}\n\n";
		}
		return $code;
	}
}
?>
