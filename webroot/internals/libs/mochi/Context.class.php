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
	
	function getAppResources() {
		return $this->appResources;
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
		$requestUri = $this->getRequestUriWithoutQuery();
		if (StringUtils::isBlank($requestUri)) return NULL;
		
		$basePath = $this->getBasePath();
		if (is_null($basePath)) return NULL;
		
		$resourcePath = str_replace($basePath, '', $requestUri);
		return StringUtils::isBlank($resourcePath) ? '/' : $resourcePath;
	}
	
	/**
	 * /base/path/to/resource => resource
	 */
	function getResourceName() {
		return self::getResourceNameFromPath($this->getResourcePath());
	}
	
	static function getResourceNameFromPath($resourcePath) {
		$pageName = $resourcePath;
		$lastSlash = strrpos($pageName, '/');
		if ($lastSlash === FALSE) return $pageName;
		return substr($pageName, $lastSlash + 1);
	}
	
	function getAppRoot() {
		return $this->appRoot;
	}
}
?>
