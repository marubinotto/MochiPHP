<?php
require_once('adodb5/adodb.inc.php');
require_once("adodb5/adodb-exceptions.inc.php"); 
require_once(dirname(__FILE__) . '/../utils/Object.class.php');
require_once(dirname(__FILE__) . '/../utils/StringUtils.class.php');

class Database extends Object
{
	private $driver;
	private $host;
	private $user;
	private $password;
	private $database;
	
	private $conn;
	private $dictionary = NULL;
	
	static $DRIVER_TO_DB = array(
		'sqlite' => 'sqlite',
		'sqlitepo' => 'sqlite',
		'mysql' => 'mysql',
		'mysqlt' => 'mysql',
		'postgres' => 'postgres'
	);
	
	function __construct(
		$driver, 
		$host, 
		$user = NULL, 
		$password = NULL, 
		$database = NULL) {
		
		$this->driver = $driver;
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		$this->database = $database;
		
		$this->conn = ADONewConnection($driver);
		$this->conn->Connect($host, $user, $password, $database);
		$this->conn->SetFetchMode(ADODB_FETCH_ASSOC); 
	}
	
	function getDatabaseName() {
		return self::$DRIVER_TO_DB[$this->driver];
	}
	
	function getDataSourceName() {
		return "{$this->driver}://{$this->user}:{$this->password}@{$this->host}/{$this->database}";
	}
	
	function getADOConnection() {
		return $this->conn;
	}
	
	function getDataDictionary() {
		if (is_null($this->dictionary)) {
			$this->dictionary = NewDataDictionary(
				$this->conn, $this->getDriverNameForDataDictionary());
		}
		return $this->dictionary;
	}
	
	private function getDriverNameForDataDictionary() {
		if ($this->getDatabaseName() === 'sqlite') {
			if (self::sqliteSupportsIfExists($this->getDatabaseVersionAsArray())) 
				return 'sqlite';
		}
		return FALSE;
	}

	static function sqliteSupportsIfExists($versionArray) {
		if (count($versionArray) < 2) return FALSE;
		return ($versionArray[0] == 3 && $versionArray[1] >= 3) || $versionArray[0] > 3;
	}
	
	function getDatabaseDescription() {
		$info = $this->conn->ServerInfo();
		return $info['description'];
	}
	
	function getDatabaseVersion() {
		$info = $this->conn->ServerInfo();
		return $info['version'];
	}
	
	function getDatabaseVersionAsArray() {
		$version = $this->getDatabaseVersion();
		return StringUtils::split($version, '.');
	}
	
	function escape($string) {
		if (is_null($string)) return NULL;
		
		$result = $this->conn->qstr($string);
		$result = StringUtils::removeStart($result, "'");
		$result = StringUtils::removeEnd($result, "'");
		
		return $result;
	}
	
	/**
	 * Returns a timestamp string in the format the database accepts
	 */
	function formatTimestamp() {
		return date("Y-m-d H:i:s");
	}
	
	/**
	 * Returns an array of table names for the current database as an array. 
	 * The array should exclude system catalog tables if possible
	 */
	function getTableNames() {
		try {
			return $this->conn->MetaTables('TABLES');
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	/**
	 * Returns an array of column names for $tableName in lowercase. 
	 */
	function getColumnNames($tableName) {
		try {
			$rawNames = $this->conn->MetaColumnNames($tableName);
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
		
		$names = array();
		foreach ($rawNames as $key => $value) {
			$names[] = strtolower($key);
		}
		return $names;
	}
	
	/**
	 * Issues a single SQL execute, typically a DDL statement. 
	 */
	function execute($sql, array $args = NULL) {
		try {
			$this->conn->Execute($sql, self::prepareArgs($args));
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}

	/**
	 * Issues a single SQL update operation and returns the number of rows affected 
	 */
	function update($sql, array $args = NULL) {
		try {
			$this->conn->Execute($sql, self::prepareArgs($args));
			return $this->conn->Affected_Rows();
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	/**
	 * Only valid field names for $tableName are applied.
	 * If $row contains keys that are invalid field names for $tableName, they are ignored. 
	 * 
	 * Returns the autonumbering ID inserted. Returns NULL if the function not supported. 
	 */
	function insertRow($tableName, $row) {
		try {
			$this->conn->AutoExecute($tableName, $row, 'INSERT');
			$id = $this->conn->Insert_ID();
			return $id ? $id : NULL;
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	/**
	 * Only valid field names for $tableName are applied.
	 * If $row contains keys that are invalid field names for $tableName, they are ignored. 
	 */
	function updateRows($tableName, $row, $where = FALSE) {
		try {
			$this->conn->AutoExecute($tableName, $row, 'UPDATE', $where);
			return $this->conn->Affected_Rows();
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	function queryForADORecordSet($sql, array $args = NULL) {
		try {
			return $this->conn->Execute($sql, self::prepareArgs($args));
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	function queryForRow($sql, array $args = NULL) {
		try {
			$row = $this->conn->GetRow($sql, self::prepareArgs($args));
			return $row ? $row : NULL;	// an array with zero elements will be converted to FALSE
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	function queryForRows($sql, array $args = NULL) {
		try {
			$rows = $this->conn->GetAll($sql, self::prepareArgs($args));
			return $rows ? $rows : array();
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	function queryForValue($sql, array $args = NULL) {
		try {
			// http://phplens.com/adodb/reference.functions.getone.html
			// If an error occur, false is returned (but ADODB_Exception is enabled)
			return $this->conn->GetOne($sql, self::prepareArgs($args));
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	function queryForValues($sql, array $args = NULL) {
		try {
			$values = $this->conn->GetCol($sql, self::prepareArgs($args));
			return $values ? $values : array();
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}

	/**
	 * http://phplens.com/lens/adodb/docs-datadict.htm
	 */
	function createTableSqlsFromADOdbDdl($tableName, $fieldDefs, array $optArray = NULL) {
		try {
			return $this->getDataDictionary()->CreateTableSQL(
				$tableName, $fieldDefs, self::prepareArgs($optArray));
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	/**
	 * @return the dropped table names
	 */
	function dropAllTables() {
		$tables = $this->getTableNames();
		try {
			foreach ($tables as $table) {
				$sqlarray = $this->getDataDictionary()->DropTableSQL($table);
				$this->getDataDictionary()->ExecuteSQLArray($sqlarray);
			}
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
		return $tables;
	}

	function dropTable($tableName) {
		try {
			$sqlarray = $this->getDataDictionary()->DropTableSQL($tableName);
			$this->getDataDictionary()->ExecuteSQLArray($sqlarray);
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}

	function dropTableIfExists($tableName) {
		if (in_array($tableName, $this->getTableNames())) {
			try {
				$this->dropTable($tableName);
			}
			catch (ADODB_Exception $e) {
				throw DatabaseException::convertException($e);
			}
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	function beginTransaction() {
		try {
			$this->conn->StartTrans();
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	function rollback() {
		try {
			$this->conn->CompleteTrans(FALSE);
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	function commit() {
		try {
			$this->conn->CompleteTrans();
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}
	}
	
	/**
	 * This method will automatically create the sequence if it does not exist.
	 */
	function nextSequenceValue($sequenceName) {
		try {
			return $this->conn->GenID($sequenceName);
		}
		catch (ADODB_Exception $e) {
			throw DatabaseException::convertException($e);
		}	
	}
	
	function dropSequenceIfExists($sequenceName) {
		try {
			$this->conn->DropSequence($sequenceName);
		}
		catch (ADODB_Exception $e) {}
	}
	
	function createLimitClause($offset, $count) {
		$database = $this->getDatabaseName();
		if ($database === 'mysql') {
			return " limit {$offset}, {$count}";
		}
		else if ($database === 'sqlite' || $database === 'postgres') {
			return " limit {$count} offset {$offset}";
		}
		else {
			throw new Exception('createLimitClause unsupported: ' . $this->driver);
		}
	}

	
// Internals

	static private function prepareArgs($args) {
		return  is_null($args) ? FALSE : $args;
	}
}

class DatabaseException extends Exception
{
	static function convertException($adodbException) {
		return self::createException(
			$adodbException->getCode(), 
			$adodbException->msg, 
			$adodbException->dbms, 
			$adodbException->sql, 
			$adodbException->params);
	}
	
	static function createException($code, $message, $driver, $sql = NULL, $args = NULL) {
		// Select an exception class for the code
		$exception = 'DatabaseException';	// as default
		if (isset(Database::$DRIVER_TO_DB[$driver])) {
			$map = call_user_func(array(
				'DatabaseException', 'map_' . Database::$DRIVER_TO_DB[$driver]));
			if (isset($map[$code])) $exception = $map[$code] . 'Exception';
		}
		return new $exception($code, $message, $driver, $sql, $args);		
	}
	
	static function map_sqlite() {
		return array(
			'19' => 'DbConstraintViolation'
		);
	}
	static function map_mysql() {
		return array(
			'1048' => 'DbConstraintViolation',		// violates not null
			'1062' => 'DbUniqueViolation'
		);
	}
	
	public $driver;
	public $sql;
	public $args;
	
	function __construct($code, $message, $driver, $sql = NULL, $args = NULL) {
		$this->driver = $driver;
        $this->sql = $sql;
        $this->args = $args;
		
		$message = "[$driver:$code] " . $message;
		if (!is_null($sql)) $message .= "\n  - $sql";
		
        parent::__construct($message, $code);
    }
}

class DbConstraintViolationException extends DatabaseException {}
class DbUniqueViolationException extends DbConstraintViolationException {}
?>
