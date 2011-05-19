<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/db/PersistentObjectRepository.class.php');
require_once('DatabaseTest.php');
require_once('PersistentObjectTest.php');

abstract class PersistentObjectRepositoryTestBase extends PHPUnit_Framework_TestCase
{
	protected $object;
	protected $database;
	
	function setUp() {
		DatabaseTestBase::avoidDuplicateADOdbLibInclude();
		$this->database = DatabaseTestBase::createCleanMySQLDatabase();
		
		PersistentObject::createTableFor("PersistentArticle", $this->database, TRUE);
		$this->object = new ArticleRepository($this->database);
	}
}

class ArticleRepository extends PersistentObjectRepository
{
	function __construct(Database $database) {
		parent::__construct($database);
	}
	
	function getObjectClassName() {
		return "PersistentArticle";
	}
}

class EmptyRepositoryTest extends PersistentObjectRepositoryTestBase
{
	function test_count() {
		$this->assertEquals(0, $this->object->count());
	}
	
	function test_newInstance() {
		$article = $this->object->newInstance();
		
		$this->assertNotNull($article);
		$this->assertTrue($article instanceof PersistentArticle);
	}
	
	function test_getTableName() {
		$this->assertEquals("article", $this->object->getTableName());
	}
}

class OneObjectRepositoryTest extends PersistentObjectRepositoryTestBase
{
	private $id;
	
	function setUp() {
		parent::setUp();
		
		$article = $this->object->newInstance();
		$article->setTitle("title");
	    $article->setContent("content");  
	    $article->setUpdateDatetime("2011-01-01 00:00:00");
	    $article->save();
	    
	    $this->id = $article->getId();
	}
	
	private function assertArticleIsTheOne($article) {
		$this->assertEquals($this->id, $article->getId());
		$this->assertEquals("title", $article->getTitle());
		$this->assertEquals("content", $article->getContent());
		$this->assertEquals("2011-01-01 00:00:00", $article->getUpdateDatetime());
	}
	
	function test_count() {
		$this->assertEquals(1, $this->object->count());
	}
	
	function test_findById() {
		$article = $this->object->findById($this->id);
		$this->assertArticleIsTheOne($article);
	}
	
	function test_findByIdWithoutCaching() {
		$article1 = $this->object->findById($this->id);
		$article2 = $this->object->findById($this->id);
		
		$this->assertNotSame($article1, $article2);
	}
	
	function test_findByIdWithCaching() {
		$article1 = $this->object->findById($this->id, TRUE);
		$article2 = $this->object->findById($this->id, TRUE);
		
		$this->assertSame($article1, $article2);
	}
	
	function test_findByUnexistingId() {
		$article = $this->object->findById(555);
		$this->assertNull($article);
	}
	
	function test_findAll() {
		$articles = $this->object->findAll();
		
		$this->assertEquals(1, count($articles));
		$this->assertArticleIsTheOne($articles[0]);
	}
	
	function test_deleteById() {
		$result = $this->object->deleteById($this->id);
		
		$this->assertTrue($result);
		$this->assertEquals(0, $this->object->count());
	}
	
	function test_deleteByUnexistingId() {
		$result = $this->object->deleteById(555);
		
		$this->assertFalse($result);
		$this->assertEquals(1, $this->object->count());
	}
	
	function test_update() {
		$article = $this->object->findById($this->id);
		$article->setContent("updated");
		
		$article->save();
		$article = $this->object->findById($this->id);
		
		$this->assertEquals($this->id, $article->getId());
		$this->assertEquals("title", $article->getTitle());
		$this->assertEquals("updated", $article->getContent());
		$this->assertEquals("2011-01-01 00:00:00", $article->getUpdateDatetime());
	}
	
	function test_InstantRepository() {
		$repository = new InstantRepository("PersistentArticle", $this->database);
		
		$article = $repository->findById($this->id);
		$this->assertArticleIsTheOne($article);
	}
}

class TwoObjectsRepositoryTest extends PersistentObjectRepositoryTestBase
{
	private $id1;
	private $id2;
	
	function setUp() {
		parent::setUp();
		
		$article = $this->object->newInstance();
		$article->setTitle("title1");
	    $article->setContent("content1");  
	    $article->setUpdateDatetime("2011-01-01 00:00:00");
	    $article->save();
	    $this->id1 = $article->getId();
		
		$article = $this->object->newInstance();
		$article->setTitle("title2");
	    $article->setContent("content2");  
	    $article->setUpdateDatetime("2011-01-02 00:00:00");
	    $article->save();
	    $this->id2 = $article->getId();
	}
	
	private function assertArticleIs1($article) {
		$this->assertEquals($this->id1, $article->getId());
		$this->assertEquals("title1", $article->getTitle());
		$this->assertEquals("content1", $article->getContent());
		$this->assertEquals("2011-01-01 00:00:00", $article->getUpdateDatetime());
	}
	
	private function assertArticleIs2($article) {
		$this->assertEquals($this->id2, $article->getId());
		$this->assertEquals("title2", $article->getTitle());
		$this->assertEquals("content2", $article->getContent());
		$this->assertEquals("2011-01-02 00:00:00", $article->getUpdateDatetime());
	}
	
	function test_countAll() {
		$this->assertEquals(2, $this->object->count());
	}
	
	function test_countByTitle() {
		$this->assertEquals(1, $this->object->count("title = ?", array("title1")));
		$this->assertEquals(1, $this->object->count("title = ?", array("title2")));
		$this->assertEquals(0, $this->object->count("title = ?", array("no-such-title")));
	}
	
	function test_findAll() {
		$articles = $this->object->findAll("update_datetime");
		
		$this->assertEquals(2, count($articles));
		$this->assertArticleIs1($articles[0]);
		$this->assertArticleIs2($articles[1]);
	}
	
	function test_findAllWithPagination() {
		$articles = $this->object->findAll("update_datetime", array('size' => 1, 'index' => 1));
		
		$this->assertTrue($articles instanceof PaginatedArray);
		$this->assertEquals(1, $articles->getCount());
		$this->assertArticleIs2($articles->elements[0]);
		$this->assertEquals(1, $articles->getPageSize());
		$this->assertEquals(1, $articles->getPageIndex());
		$this->assertEquals(2, $articles->getTotalCount());
	}
	
	function test_findAllDesc() {
		$articles = $this->object->findAll("update_datetime desc");
		
		$this->assertEquals(2, count($articles));
		$this->assertArticleIs2($articles[0]);
		$this->assertArticleIs1($articles[1]);
	}
	
	function test_find() {
		$articles = $this->object->find("title = ?", array("title2"));
		
		$this->assertEquals(1, count($articles));
		$this->assertArticleIs2($articles[0]);
	}
	
	function test_delete() {
		$deleted = $this->object->delete("title = ?", array("title1"));
		
		$this->assertEquals(1, $deleted);
		$this->assertEquals(1, $this->object->count());
		
		$articles = $this->object->findAll();
		$this->assertArticleIs2($articles[0]);
	}
	
	function test_deleteById() {
		$result = $this->object->deleteById($this->id1);
		
		$this->assertTrue($result);
		$this->assertEquals(1, $this->object->count());
		
		$articles = $this->object->findAll();
		$this->assertArticleIs2($articles[0]);
	}
	
	function test_deleteAll() {
		$deleted = $this->object->deleteAll();
		
		$this->assertEquals(2, $deleted);
		$this->assertEquals(0, $this->object->count());
	}
}
?>
