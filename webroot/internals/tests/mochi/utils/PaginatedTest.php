<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/utils/Paginated.class.php');

class PaginatedTest extends PHPUnit_Framework_TestCase
{
	function test_calculatePageCount() {
		$this->assertEquals(0, Paginated::calculatePageCount(0, 2));
		$this->assertEquals(1, Paginated::calculatePageCount(1, 2));
		$this->assertEquals(1, Paginated::calculatePageCount(2, 2));
		$this->assertEquals(2, Paginated::calculatePageCount(3, 2));
		$this->assertEquals(2, Paginated::calculatePageCount(4, 2));
		$this->assertEquals(3, Paginated::calculatePageCount(5, 2));
	}
	
	function test_getNeighborPageIndexes() {
		$this->assertEquals(array(0), Paginated::_getNeighborPageIndexes(0, 1, 3));
		$this->assertEquals(array(0, 1), Paginated::_getNeighborPageIndexes(0, 2, 3));
		$this->assertEquals(array(0, 1, 2), Paginated::_getNeighborPageIndexes(0, 3, 3));
		$this->assertEquals(array(0, 1, 2), Paginated::_getNeighborPageIndexes(0, 4, 3));
		
		$this->assertEquals(array(0, 1, 2), Paginated::_getNeighborPageIndexes(1, 6, 3));
		$this->assertEquals(array(1, 2, 3), Paginated::_getNeighborPageIndexes(2, 6, 3));
		$this->assertEquals(array(2, 3, 4), Paginated::_getNeighborPageIndexes(3, 6, 3));
		$this->assertEquals(array(3, 4, 5), Paginated::_getNeighborPageIndexes(4, 6, 3));
		$this->assertEquals(array(3, 4, 5), Paginated::_getNeighborPageIndexes(5, 6, 3));
	}
}

class PaginatedArrayTest extends PHPUnit_Framework_TestCase
{
	// |[0-]|
	function test_oneOfOne() {
		$page = new PaginatedArray(array(1), 2, 0, 1);
		
		$this->assertEquals(1, $page->getCount());
		$this->assertEquals(1, $page->getPageCount());
		$this->assertEquals(0, $page->getIndexOfFirstElement());
		$this->assertEquals(0, $page->getIndexOfLastElement());
		$this->assertTrue($page->isFirstPage());
		$this->assertTrue($page->isLastPage());
	}
	
	// |[00]|
	function test_oneOfOne_full() {
		$page = new PaginatedArray(array(1, 1), 2, 0, 2);
		
		$this->assertEquals(2, $page->getCount());
		$this->assertEquals(1, $page->getPageCount());
		$this->assertEquals(0, $page->getIndexOfFirstElement());
		$this->assertEquals(1, $page->getIndexOfLastElement());
		$this->assertTrue($page->isFirstPage());
		$this->assertTrue($page->isLastPage());
	}
	
	// |[00]|0-|
	function test_oneOfTwo() {
		$page = new PaginatedArray(array(1, 1), 2, 0, 3);
		
		$this->assertEquals(2, $page->getCount());
		$this->assertEquals(2, $page->getPageCount());
		$this->assertEquals(0, $page->getIndexOfFirstElement());
		$this->assertEquals(1, $page->getIndexOfLastElement());
		$this->assertTrue($page->isFirstPage());
		$this->assertFalse($page->isLastPage());
	}
	
	// |00|[0-]|
	function test_twoOfTwo() {
		$page = new PaginatedArray(array(1), 2, 1, 3);
		
		$this->assertEquals(1, $page->getCount());
		$this->assertEquals(2, $page->getPageCount());
		$this->assertEquals(2, $page->getIndexOfFirstElement());
		$this->assertEquals(2, $page->getIndexOfLastElement());
		$this->assertFalse($page->isFirstPage());
		$this->assertTrue($page->isLastPage());
	}
}
?>
