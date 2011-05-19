<?php
require_once('Object.class.php');

abstract class Paginated extends Object
{
	/**
	 * Returns the size that represents capacity of this page.
	 */
	abstract function getPageSize();
	
	/**
	 * Returns the index of this page in the whole of the list.
     * Page numbering starts with 0.
	 */
	abstract function getPageIndex();
	
	/**
	 * Returns the number of the elements in this page.
	 */
	abstract function getCount();
	
	/**
	 * Returns the number of the elements in the whole of the list.
	 */
	abstract function getTotalCount();
	
	/**
	 * Returns the number of the pages in the whole of the list.
	 */
	function getPageCount() {
		return self::calculatePageCount($this->getTotalCount(), $this->getPageSize());
	}
	
	/**
	 * Returns the index of the first element of this page in the whole of the list.
     * If no elements are contained, a negative value will be returned.
	 */
	function getIndexOfFirstElement() {
		if ($this->getCount() == 0) {
            return -1;
        }
        return $this->getPageSize() * $this->getPageIndex();
	}
	
	/**
	 * Returns the index of the end element of this page in the whole of the list.
     * If no elements are contained, a negative value will be returned.
	 */
	function getIndexOfLastElement() {
		if ($this->getCount() == 0) {
            return -1;
        }
		return ($this->getIndexOfFirstElement() + $this->getCount()) - 1;
	}
	
	/**
	 * Returns true if this page is the first of all.
	 */
	function isFirstPage() {
		return $this->getPageIndex() == 0;
	}
	
	/**
	 * Returns true if this page is the last of all.
	 */
	function isLastPage() {
		if ($this->getPageCount() == 0) {
            return TRUE;
        }
        return $this->getPageIndex() == ($this->getPageCount() - 1);
	}
	
	function getNeighborPageIndexes($size = 5) {
		return self::_getNeighborPageIndexes(
			$this->getPageIndex(), $this->getPageCount(), $size);
	}
	
	static function calculatePageCount($total, $pageSize) {
		if ($total == 0) {
            return 0;
        } 
        else if ($total < $pageSize) {
            return 1;
        } 
        else {
            $count = intval($total / $pageSize);
            if (($total % $pageSize) > 0) {
                $count++;
            }
            return $count;
        }
	}
	
	static function _getNeighborPageIndexes($currentIndex, $pageCount, $size) {
		$countOthers = $size - 1;
		$oneSide = intval($countOthers / 2);
		$first = $currentIndex - $oneSide;
		$last = $currentIndex + $oneSide + ($countOthers % 2);
		
		if ($first < 0) $last += abs($first);
		if ($last >= $pageCount) $first -= ($last - $pageCount + 1);
		
		return range(max(0, $first), min($pageCount - 1, $last));
	}
}

class PaginatedArray extends Paginated
{
	public $elements;
	
	private $pageSize;
	private $pageIndex;
	private $totalCount;
	
	function __construct(array $elements, $pageSize, $pageIndex, $totalCount) {
		$this->elements = $elements;
		$this->pageSize = $pageSize;
		$this->pageIndex = $pageIndex;
		$this->totalCount = $totalCount;
	}
	
	function getPageSize() {
		return $this->pageSize;
	}
	
	function getPageIndex() {
		return $this->pageIndex;
	}
	
	function getCount() {
		return count($this->elements);
	}
	
	function getTotalCount() {
		return $this->totalCount;
	}
}
?>
