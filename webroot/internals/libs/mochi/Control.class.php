<?php
require_once('utils/Object.class.php');
require_once('utils/StringUtils.class.php');
require_once('utils/Messages.class.php');

/**
 * Work flow with a request:
 *  1) Control::setMessages
 *  2) __construct
 *  3) onPrepare
 *  4) setState (with validation)
 *  5) dispatchEvent
 *  6) onRender
 *  7) render (on a template)
 */
abstract class Control extends Object
{
	private $name;
	private $displayName;
	protected $id;
	
	function __construct($name, array $attributes = NULL) {
		$this->setName($name);
		if (!is_null($attributes)) $this->bindValues($attributes);
	}
	
	function getName() {
		return $this->name;
	}
	
	function setName($name) {
		$this->name = $name;
	}
	
	function getDisplayName() {
		if (!is_null($this->displayName)) 
			return $this->displayName;
		return $this->getMessage($this->getName() . '.display-name');
	}
	
	function setDisplayName($displayName) {
		$this->displayName = $displayName;
	}
	
	function getId() {
		return $this->id;
	}
	
	function setId($id) {
		return $this->id = $id;
	}
	
	function onPrepare(Context $context) {
	}
	
	function setState(Context $context) {
	}
	
	function dispatchEvent(Context $context) {
		return TRUE;
	}
	
	function onRender(Context $context) {
	}
	
	abstract function render();
	
	static private $messages;
	
	static function setMessages($messages) {
		self::$messages = $messages;
	}
	
	protected function getMessage($name, array $args = NULL) {
		if (is_null(self::$messages)) self::$messages = new Messages();
		return self::$messages->get($name, $args);
	}
}
?>
