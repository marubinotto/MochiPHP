<?php
require_once('utils/Object.class.php');
require_once('utils/StringUtils.class.php');

/**
 * AppResources defines the application directory structure and 
 * allows to get resources without include_path setting.
 */
class AppResources extends Object
{
	private $appRoot;	// the absolute path of the app document root
	
	const RESOURCE_ROOT = 'internals/app';
	
	const PAGES_DIR = 'pages';
	const PAGE_404_FILE = 'error404.php';
	const PAGE_ERROR_FILE = 'error.php';
	
	const CONFIG_DIR = 'config';
	const MESSAGES_FILE = 'messages.php';
	const FACTORY_FILE = 'factory.php';
	const FACTORY_CLASS_NAME = 'Factory';
	
	const TEMPLATE_DIR = 'templates';
	
	function __construct($appRoot) {
		$this->appRoot = $appRoot;
	}
	
	static function joinPath($path1, $path2) {
		$separator = '/';
		return StringUtils::removeEnd($path1, $separator) . 
			$separator . StringUtils::removeStart($path2, $separator);
	}
	
	function getResourceRootPath() {
		return self::joinPath($this->appRoot, self::RESOURCE_ROOT);
	}
	
	function createObject($className, $filePath, $arg = NULL) {
		$fullFilePath = self::joinPath($this->getResourceRootPath(), $filePath);
		
		include_once($fullFilePath);		
		if (!class_exists($className)) return NULL;
		
		if (is_null($arg))
			return new $className();
		else 
			return new $className($arg);
	}
	
	// pages
	
	function getPagesDirPath() {
		return self::joinPath($this->getResourceRootPath(), self::PAGES_DIR);
	}
	
	function getPageFilePathIfExists($filePath) {
		$fullPath = self::joinPath($this->getPagesDirPath(), $filePath);
		return is_file($fullPath) ? $fullPath : NULL;
	}
	
	/**
	 * Returns NULL if the file does not exist.
	 */
	function get404PageFilePath() {
		return $this->getPageFilePathIfExists(self::PAGE_404_FILE);
	}
	
	function getErrorPageFilePath() {
		return $this->getPageFilePathIfExists(self::PAGE_ERROR_FILE);
	}
	
	function createPageObject($className, $filePath) {
		return $this->createObject($className, self::joinPath(self::PAGES_DIR, $filePath));
	}
	
	// config
	
	function getConfigDirPath() {
		return self::joinPath($this->getResourceRootPath(), self::CONFIG_DIR);
	}
	
	function loadConfig($filePath) {
		return require(self::joinPath($this->getConfigDirPath(), $filePath));
	}
	
	function loadMessages() {
		return $this->loadConfig(self::MESSAGES_FILE);
	}
	
	function createFactory() {
		return $this->createObject(
			self::FACTORY_CLASS_NAME, 
			self::joinPath(self::CONFIG_DIR, self::FACTORY_FILE),
			$this);
	}
	
	// templates
	
	function getTemplateDirPath() {
		return self::joinPath($this->getResourceRootPath(), self::TEMPLATE_DIR);
	}
}
?>
