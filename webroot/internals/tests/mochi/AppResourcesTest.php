<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/AppResources.class.php');
require_once('mochi/Page.class.php');

class AppResourcesTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $appRoot;
	
	function setUp() {
		$this->appRoot = dirname(__FILE__) . "/mockapp";
		$this->object = new AppResources($this->appRoot);
	}
	
	// joinPath
	
	function test_joinPath() {
		$this->assertEquals("/path/to/file", AppResources::joinPath("/path/to", "file"));
		$this->assertEquals("/path/to/file", AppResources::joinPath("/path/to", "/file"));
		$this->assertEquals("/path/to/file", AppResources::joinPath("/path/to/", "file"));
		$this->assertEquals("/path/to/file", AppResources::joinPath("/path/to/", "/file"));
	}
	
	// resource root
	
	function test_getResourceRootPath() {
		$this->assertEquals(
			$this->appRoot . "/internals/app", 
			$this->object->getResourceRootPath());
	}
	
	// createObject
	
	function test_createUnexistingObject() {
		$result = $this->object->createObject("Hogehoge", "/models/Person.class.php");
		$this->assertNull($result);
	}
	
	function test_createObject() {
		$result = $this->object->createObject("Person", "/models/Person.class.php");
		$this->assertEquals("Good afternoon!", $result->greet());
	}
	
	function test_createObjectWithArg() {
		$result = $this->object->createObject("Person", "/models/Person.class.php", "Hello world!");
		$this->assertEquals("Hello world!", $result->greet());
	}
	
	// pages
	
	function test_getPagesDirPath() {
		$this->assertEquals(
			$this->appRoot . "/internals/app/pages", 
			$this->object->getPagesDirPath());
	}
	
	function test_getExistingPageFilePath() {
		$this->assertEquals(
			$this->appRoot . "/internals/app/pages/hello.php", 
			$this->object->getPageFilePathIfExists("/hello.php"));
	}
	
	function test_getUnexistingPageFilePath() {
		$this->assertNull($this->object->getPageFilePathIfExists("/no-such-page.php"));
	}

	function test_404PageFilePathIsNullByDefault() {
		$this->assertNull($this->object->get404PageFilePath());
	}
	
	function test_errorPageFilePathIsNullByDefault() {
		$this->assertNull($this->object->getErrorPageFilePath());
	}
	
	function test_createPageObject() {
		$object = $this->object->createPageObject("TestPage", "test.php");
		
		$this->assertNotNull($object);
		$this->assertEquals("TestPage", $object->getClass()->getName());
		$this->assertTrue($object instanceof Page);
	}
	
	// config
	
	function test_getConfigDirPath() {
		$this->assertEquals(
			$this->appRoot . "/internals/app/config", 
			$this->object->getConfigDirPath());
	}
	
	function test_createFactory() {
		$factory = $this->object->createFactory();
		
		$this->assertSame($this->object, $factory->appResources);
		$this->assertEquals(0, $factory->getPersonRepository()->size());
	}
	
	function test_getSettings() {
		$settings = $this->object->getSettings();
		$this->assertEquals("home", $settings->get("system.page.default"));
		
		// the setting object should be cached
		$this->assertSame($settings, $this->object->getSettings());
	}
	
	function test_loadMessages() {
		$messages = $this->object->loadMessages();
		$this->assertEquals("hello", $messages["message"]);
	}
	
	// templates
	
	function test_getTemplateDirPath() {
		$this->assertEquals(
			$this->appRoot . "/internals/app/templates", 
			$this->object->getTemplateDirPath());
	}
}
?>
