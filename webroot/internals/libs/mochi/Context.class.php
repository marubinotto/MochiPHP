<?php
require_once('utils/Object.class.php');
require_once('utils/StringUtils.class.php');
require_once('AppResources.class.php');
require_once('Session.class.php');

class Context extends Object
{
	const FRONT_SCRIPT_NAME = "front.php";
	
	private $parameters;
	private $session;
	private $serverVars;
	private $appRoot;
	private $appResources;
	
	function __construct(
		array $parameters = array(), 
		array $serverVars = array(),
		$appRoot = NULL) {
			
		$this->parameters = $parameters;
		$this->serverVars = $serverVars;
		
		if (!is_null($appRoot)) $this->appRoot = $appRoot;
		else {
			$scriptPath = $serverVars['SCRIPT_FILENAME'];
			if (!is_null($scriptPath)) $this->appRoot = dirname($scriptPath);
		}
		
		$this->appResources = new AppResources($this->appRoot);
		$this->session = new Session();
	}
	
	function getParameters() {
		return $this->parameters;
	}
	
	function getParameter($name) {
		if (!isset($this->parameters[$name])) return NULL;
		return $this->parameters[$name];
	}
	
	function getSession() {
		return $this->session;
	}
	
	/**
	 * For switching the implementation of Session.
	 */
	function setSession($session) {
		$this->session = $session;
	}
	
	function getAppRoot() {
		return $this->appRoot;
	}
	
	function getAppResources() {
		return $this->appResources;
	}
	
	function getSettings() {
		return $this->appResources->getSettings();
	}
	
	function getServerVar($name) {
		if (!isset($this->serverVars[$name])) return NULL;
		return $this->serverVars[$name];
	}
	
	function getHost() {
		return $this->getServerVar('HTTP_HOST');
	}
	
	function getReferer() {
		return $this->getServerVar('HTTP_REFERER');
	}
	 
	function getRequestUriWithoutQuery() {
		$requestUri = $this->getServerVar('REDIRECT_URL');
		if (is_null($requestUri)) 
			$requestUri = $this->getServerVar('REQUEST_URI');
			
		// delete a query string
		$pos = strpos($requestUri, '?');
		if ($pos) $requestUri = substr($requestUri, 0, $pos);
		
		return $requestUri;
	}
		
	/**
	 * /test/front.php => /test
	 */
	function getBasePath() {
		$scriptName = $this->getServerVar('SCRIPT_NAME');
		if (StringUtils::isBlank($scriptName)) return NULL;
		return StringUtils::removeEnd($scriptName, '/' . self::FRONT_SCRIPT_NAME);
	}

	/**
	 * /base/path/to/resource => /path/to/resource
	 */
	function getResourcePath() {
		$pathObject = $this->getResourcePathObject();
		return is_null($pathObject) ? NULL : $pathObject->getPath();
	}
	
	function getResourcePathObject() {
		$requestUri = $this->getRequestUriWithoutQuery();
		if (StringUtils::isBlank($requestUri)) return NULL;
		
		$basePath = $this->getBasePath();
		if (is_null($basePath)) return NULL;
		
		$resourcePath = str_replace($basePath, '', $requestUri);
		$resourcePath = StringUtils::isBlank($resourcePath) ? '/' : $resourcePath;
		return new ResourcePath($resourcePath);
	}
}

class ResourcePath
{
	const PATH_SEPARATOR = '/';
	private $path;
	
	function __construct($path) {
		$this->path = $path;
	}
	
	function getPath() {
		return $this->path;
	}
	
	function getName() {
		$lastSlash = strrpos($this->path, self::PATH_SEPARATOR);
		if ($lastSlash === FALSE) return $this->path;
		return substr($this->path, $lastSlash + 1);
	}
	
	function getDirectoryPath() {
		$lastSlash = strrpos($this->path, self::PATH_SEPARATOR);
		if ($lastSlash === FALSE) return NULL;
		return substr($this->path, 0, $lastSlash + 1);
	}
	
	function isDirectory() {
		return StringUtils::isBlank($this->getName());
	}
	
	function withAnotherName($name) {
		return new ResourcePath($this->getDirectoryPath() . $name);
	}
	
	function __toString() {
		return $this->getPath();
	}
}

class MockContext extends Context
{
	private $resourcePathObject;
	private $settings;
	
	function __construct($resourcePath, array $settings = array()) {
		$this->resourcePathObject = new ResourcePath($resourcePath);
		$this->settings = new ArrayWrapper($settings);
	}
	
	function getResourcePathObject() {
		return $this->resourcePathObject;
	}
	
	function getSettings() {
		return $this->settings;
	}
}
?>
