<?php

namespace ECSPrefix20210508\Nette\Utils;

use ECSPrefix20210508\Nette;
/**
 * Provides the base class for a generic list (items can be accessed by index).
 */
class ArrayList implements \ArrayAccess, \Countable, \IteratorAggregate
{
    use Nette\SmartObject;
    /** @var mixed[] */
    private $list = [];
    /**
     * Returns an iterator over all items.
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->list);
    }
    /**
     * Returns items count.
     * @return int
     */
    public function count()
    {
        return \count($this->list);
    }
    /**
     * Replaces or appends a item.
     * @param  int|null  $index
     * @param  mixed  $value
     * @throws Nette\OutOfRangeException
     * @return void
     */
    public function offsetSet($index, $value)
    {
        if ($index === null) {
            $this->list[] = $value;
        } elseif (!\is_int($index) || $index < 0 || $index >= \count($this->list)) {
            throw new \ECSPrefix20210508\Nette\OutOfRangeException('Offset invalid or out of range');
        } else {
            $this->list[$index] = $value;
        }
    }
    /**
     * Returns a item.
     * @param  int  $index
     * @return mixed
     * @throws Nette\OutOfRangeException
     */
    public function offsetGet($index)
    {
        $index = (int) $index;
        if (!\is_int($index) || $index < 0 || $index >= \count($this->list)) {
            throw new \ECSPrefix20210508\Nette\OutOfRangeException('Offset invalid or out of range');
        }
        return $this->list[$index];
    }
    /**
     * Determines whether a item exists.
     * @param  int  $index
     * @return bool
     */
    public function offsetExists($index)
    {
        $index = (int) $index;
        return \is_int($index) && $index >= 0 && $index < \count($this->list);
    }
    /**
     * Removes the element at the specified position in this list.
     * @param  int  $index
     * @throws Nette\OutOfRangeException
     * @return void
     */
    public function offsetUnset($index)
    {
        $index = (int) $index;
        if (!\is_int($index) || $index < 0 || $index >= \count($this->list)) {
            throw new \ECSPrefix20210508\Nette\OutOfRangeException('Offset invalid or out of range');
        }
        \array_splice($this->list, $index, 1);
    }
    /**
     * Prepends a item.
     * @param  mixed  $value
     * @return void
     */
    public function prepend($value)
    {
        $first = \array_slice($this->list, 0, 1);
        $this->offsetSet(0, $value);
        \array_splice($this->list, 1, 0, $first);
    }
}
