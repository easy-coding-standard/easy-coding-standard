<?php

namespace ECSPrefix20210507\Nette\Utils;

use ECSPrefix20210507\Nette;
/**
 * Paginating math.
 *
 * @property   int $page
 * @property-read int $firstPage
 * @property-read int|null $lastPage
 * @property   int $base
 * @property-read bool $first
 * @property-read bool $last
 * @property-read int|null $pageCount
 * @property   int $itemsPerPage
 * @property   int|null $itemCount
 * @property-read int $offset
 * @property-read int|null $countdownOffset
 * @property-read int $length
 */
class Paginator
{
    use Nette\SmartObject;
    /** @var int */
    private $base = 1;
    /** @var int */
    private $itemsPerPage = 1;
    /** @var int */
    private $page = 1;
    /** @var int|null */
    private $itemCount;
    /**
     * Sets current page number.
     * @return static
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }
    /**
     * Returns current page number.
     * @return int
     */
    public function getPage()
    {
        return $this->base + $this->getPageIndex();
    }
    /**
     * Returns first page number.
     * @return int
     */
    public function getFirstPage()
    {
        return $this->base;
    }
    /**
     * Returns last page number.
     * @return int|null
     */
    public function getLastPage()
    {
        return $this->itemCount === null ? null : $this->base + \max(0, $this->getPageCount() - 1);
    }
    /**
     * Sets first page (base) number.
     * @return static
     * @param int $base
     */
    public function setBase($base)
    {
        $this->base = $base;
        return $this;
    }
    /**
     * Returns first page (base) number.
     * @return int
     */
    public function getBase()
    {
        return $this->base;
    }
    /**
     * Returns zero-based page number.
     * @return int
     */
    protected function getPageIndex()
    {
        $index = \max(0, $this->page - $this->base);
        return $this->itemCount === null ? $index : \min($index, \max(0, $this->getPageCount() - 1));
    }
    /**
     * Is the current page the first one?
     * @return bool
     */
    public function isFirst()
    {
        return $this->getPageIndex() === 0;
    }
    /**
     * Is the current page the last one?
     * @return bool
     */
    public function isLast()
    {
        return $this->itemCount === null ? \false : $this->getPageIndex() >= $this->getPageCount() - 1;
    }
    /**
     * Returns the total number of pages.
     * @return int|null
     */
    public function getPageCount()
    {
        return $this->itemCount === null ? null : (int) \ceil($this->itemCount / $this->itemsPerPage);
    }
    /**
     * Sets the number of items to display on a single page.
     * @return static
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = \max(1, $itemsPerPage);
        return $this;
    }
    /**
     * Returns the number of items to display on a single page.
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }
    /**
     * Sets the total number of items.
     * @return static
     * @param int $itemCount
     */
    public function setItemCount($itemCount = null)
    {
        $this->itemCount = $itemCount === null ? null : \max(0, $itemCount);
        return $this;
    }
    /**
     * Returns the total number of items.
     * @return int|null
     */
    public function getItemCount()
    {
        return $this->itemCount;
    }
    /**
     * Returns the absolute index of the first item on current page.
     * @return int
     */
    public function getOffset()
    {
        return $this->getPageIndex() * $this->itemsPerPage;
    }
    /**
     * Returns the absolute index of the first item on current page in countdown paging.
     * @return int|null
     */
    public function getCountdownOffset()
    {
        return $this->itemCount === null ? null : \max(0, $this->itemCount - ($this->getPageIndex() + 1) * $this->itemsPerPage);
    }
    /**
     * Returns the number of items on current page.
     * @return int
     */
    public function getLength()
    {
        return $this->itemCount === null ? $this->itemsPerPage : \min($this->itemsPerPage, $this->itemCount - $this->getPageIndex() * $this->itemsPerPage);
    }
}
