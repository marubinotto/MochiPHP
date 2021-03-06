<?php
require_once('utils/Object.class.php');
require_once('utils/StringUtils.class.php');
require_once('utils/Messages.class.php');
require_once('Context.class.php');
require_once('Control.class.php');

class FrontController extends Object
{
	public $pageResolver;
	private $messages;
	
	function __construct() {
		$this->pageResolver = new PageResolver();
	}
	
	function processRequest(Context $context) {
		ob_start();
		self::setUpAssert();
		try{
			// load messages
			$this->messages = new Messages(
				$context->getAppResources()->loadMessages());
		
			// create and render a page
			$page = $this->pageResolver->getPageInfo($context)->create($context);
			$this->initPage($page, $context);
			self::renderPage($page, $context);
			
			// redirect if required
			$redirect = $page->getRedirect();
			if (!is_null($redirect)) self::sendRedirect($redirect, $context);
		}
		catch (PageNotFoundException $e) {
			self::show404Page($context);
		}
		catch (Exception $e) {
			self::showErrorPage($e, $context);
		}
		ob_end_flush();
	}
	
	private static function setUpAssert() {
		assert_options(ASSERT_ACTIVE, 1);
		assert_options(ASSERT_WARNING, 0);
		assert_options(ASSERT_QUIET_EVAL, 1);
		
		function frontController_assertHandler($file, $line, $code) {
			throw new Exception("Assertion Failed: <$code> $file ($line)");
		}

		assert_options(ASSERT_CALLBACK, 'frontController_assertHandler');
	}
	
	private function initPage($page, Context $context) {
		// messages
		Control::setMessages($this->messages);
		$page->setMessages($this->messages);
		
		// factory
		$factory = $context->getAppResources()->createFactory();
		if (!is_null($factory)) $page->setFactory($factory);
			
		// parameters to public properties
		$page->setPublicPropertyValues($context->getParameters());
	}
	
	private static function renderPage($page, Context $context) {
		$permitted = $page->onPermissionCheck($context);
		if ($permitted) {
	    	$page->onPrepare($context);
	    	$continue = $page->processRequest($context);
	    	if ($continue) $page->render($context);
		}
	}
	
	private static function sendRedirect($redirect, Context $context) {
		ob_clean();
		header("Location: " . self::toRedirectLocation($redirect, $context));
		exit;
	}
	
	static function toRedirectLocation($redirect, Context $context) {
		$location = $redirect;
		if (StringUtils::startsWith($location, '/'))
			$location = $context->getBasePath() . $location;
		return $location;
	}
	
	private static function show404Page($context) {
		ob_clean();
		header("HTTP/1.1 404 Not Found");
		
		$app404Path = $context->getAppResources()->get404PageFilePath();
		if (!is_null($app404Path))
			require_once($app404Path);
		else 
			require_once('error404.php');	// default provided by this framework
		exit;
	}
	
	private static function showErrorPage($exception, $context) {
		ob_clean();
		$appErrorPagePath = $context->getAppResources()->getErrorPageFilePath();
		if (!is_null($appErrorPagePath))
			require_once($appErrorPagePath);
		else 
			require_once('error.php');	// default provided by this framework
		exit;
	}
}

class PageInfo extends Object
{
	public $filePath;
	public $className;
	public $templateName;
	
	function __construct($filePath, $className, $templateName) {
		$this->filePath = $filePath;
		$this->className = $className;
		$this->templateName = $templateName;
	}
	
	function create(Context $context) {
		$page = $context->getAppResources()->createPageObject($this->className, $this->filePath);
		if (is_null($page)) throw new PageNotFoundException($context->getResourcePath());
		$page->setTemplateName($this->templateName);
		return $page;
	}
}

class PageResolver extends Object
{
	function getPageInfo(Context $context) {
		$pathObject = $context->getResourcePathObject();
		
		if ($pathObject->isDirectory()) {
			$defaultPage = $context->getSettings()->get('system.page.default');
			if (is_null($defaultPage))
				throw new PageNotFoundException($pathObject->getPath());
			else
				$pathObject = $pathObject->withAnotherName($defaultPage);
		}
		
		$filePath = $pathObject->getPath() . '.php';
		$className = self::resourceNameToClassName($pathObject->getName());
		$templateName = StringUtils::removeStart($pathObject->getPath(), '/');
		return new PageInfo($filePath, $className, $templateName);
	}
	
	// edit-customer => EditCustomerPage
	static function resourceNameToClassName($resourceName) {
		$className = '';
		foreach (StringUtils::split($resourceName, '-') as $token) {
			$className .= StringUtils::toUpperCaseFirstChar($token);
		}
		return $className . 'Page';
	}
}

class PageNotFoundException extends Exception
{
	public function __construct($resourcePath, $code = 0) {
        parent::__construct("Page not found: " . $resourcePath, $code);
    }
}
?>
