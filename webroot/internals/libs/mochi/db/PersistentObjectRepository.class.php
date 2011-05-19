<?php
require_once('Database.class.php');
require_once('PersistentObject.class.php');
require_once(dirname(__FILE__) . '/../utils/Paginated.class.php');

abstract class PersistentObjectRepository
{
	private $database;
	
	function __construct(Database $database) {
		$this->database = $database;
	}
	
	abstract function getObjectClassName();
	
	function getDatabase() {
		return $this->database;
	}
	
	function getTableName() {
		return PersistentObject::getTableNameFor($this->getObjectClassName());
	}
	
	function newInstance() {
		$className = $this->getObjectClassName();
		$instance = new $className();
		$instance->setDatabase($this->getDatabase());
		$instance->setRepository($this);
		return $instance;
	}
	
	protected function restoreInstance($row) {
		$instance = $this->newInstance();
		$instance->bindDatabaseRow($row);
		return $instance;
	}
	
	function escape($string) {
		return $this->getDatabase()->escape($string);
	}
	
	function count($where = NULL, array $args = NULL) {
		$sql = 'select count(*) from ' . $this->getTableName();
		if (!is_null($where)) $sql .= ' where ' . $where;
		return intval($this->getDatabase()->queryForValue($sql, $args));
	}
	
	function find($where = NULL, array $args = NULL, $orderBy = NULL, array $pagination = NULL) {
		$rows = $this->getDatabase()->queryForRows(
			$this->createSelectSql($where, $orderBy, $pagination), $args);
		$objects = array();
		foreach ($rows as $row) {
			$objects[] = $this->restoreInstance($row);
		}
		if (is_null($pagination)) {
			return $objects;
		}
		else {
			$totalCount = $this->count($where, $args);
			return new PaginatedArray($objects, $pagination['size'], $pagination['index'], $totalCount);
		}
	}
	
	function findOne($where = NULL, array $args = NULL, $orderBy = NULL) {
		$row = $this->getDatabase()->queryForRow(
			$this->createSelectSql($where, $orderBy), $args);
		return is_null($row) ? NULL : $this->restoreInstance($row);
	}
		
	function findAll($orderBy = NULL, array $pagination = NULL) {
		return $this->find(NULL, NULL, $orderBy, $pagination);
	}
	
	static $CACHE_BY_ID = array();
	
	function findById($id, $cache = FALSE) {
		if ($cache) {
			$cacheKey = $this->getObjectClassName() . '_' . $id;
			if (isset(self::$CACHE_BY_ID[$cacheKey]))
				return self::$CACHE_BY_ID[$cacheKey];
		}	
		$object = $this->findOne('id = ?', array($id));
		if ($cache) {
			self::$CACHE_BY_ID[$cacheKey] = $object;
		}
		return $object;
	}
	
	protected  function createSelectSql($where = NULL, $orderBy = NULL, array $pagination = NULL) {
		$sql = 'select * from ' . $this->getTableName();
		if (!is_null($where)) $sql .= ' where ' . $where;
		if (!is_null($orderBy)) $sql .= ' order by ' . $orderBy;
		if (!is_null($pagination)) $sql .= $this->createLimitClause($pagination);
		return $sql;
	}
	
	function createLimitClause(array $pagination) {
		if (is_null($pagination)) return '';
		return $this->getDatabase()->createLimitClause(
			$pagination['size'] * $pagination['index'], $pagination['size']);
	}
	
	function delete($where = NULL, array $args = NULL) {
		$sql = 'delete from ' . $this->getTableName();
		if (!is_null($where)) $sql .= ' where ' . $where;
		return $this->getDatabase()->update($sql, $args);
	}
	
	function deleteAll() {
		return $this->delete();
	}
	
	function deleteById($id) {
		return $this->delete('id = ?', array($id)) > 0;
	}
}

class InstantRepository extends PersistentObjectRepository
{
	private $objectClassName;
	
	function __construct($objectClassName, Database $database) {
		parent::__construct($database);
		$this->objectClassName = $objectClassName;
	}
	
	function getObjectClassName() {
		return $this->objectClassName;
	}
}
?>
