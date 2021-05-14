<?php

namespace ECSPrefix20210514\Nette\Iterators;

use ECSPrefix20210514\Nette;
/**
 * Smarter caching iterator.
 *
 * @property-read bool $first
 * @property-read bool $last
 * @property-read bool $empty
 * @property-read bool $odd
 * @property-read bool $even
 * @property-read int $counter
 * @property-read mixed $nextKey
 * @property-read mixed $nextValue
 */
class CachingIterator extends \CachingIterator implements \Countable
{
    use Nette\SmartObject;
    /** @var int */
    private $counter = 0;
    public function __construct($iterator)
    {
        if (\is_array($iterator) || $iterator instanceof \stdClass) {
            $iterator = new \ArrayIterator($iterator);
        } elseif ($iterator instanceof \IteratorAggregate) {
            do {
                $iterator = $iterator->getIterator();
            } while ($iterator instanceof \IteratorAggregate);
            \assert($iterator instanceof \Iterator);
        } elseif ($iterator instanceof \Iterator) {
        } elseif ($iterator instanceof \Traversable) {
            $iterator = new \IteratorIterator($iterator);
        } else {
            throw new \ECSPrefix20210514\Nette\InvalidArgumentException(\sprintf('Invalid argument passed to %s; array or Traversable expected, %s given.', self::class, \is_object($iterator) ? \get_class($iterator) : \gettype($iterator)));
        }
        parent::__construct($iterator, 0);
    }
    /**
     * Is the current element the first one?
     * @param int $gridWidth
     * @return bool
     */
    public function isFirst($gridWidth = null)
    {
        return $this->counter === 1 || $gridWidth && $this->counter !== 0 && ($this->counter - 1) % $gridWidth === 0;
    }
    /**
     * Is the current element the last one?
     * @param int $gridWidth
     * @return bool
     */
    public function isLast($gridWidth = null)
    {
        return !$this->hasNext() || $gridWidth && $this->counter % $gridWidth === 0;
    }
    /**
     * Is the iterator empty?
     * @return bool
     */
    public function isEmpty()
    {
        return $this->counter === 0;
    }
    /**
     * Is the counter odd?
     * @return bool
     */
    public function isOdd()
    {
        return $this->counter % 2 === 1;
    }
    /**
     * Is the counter even?
     * @return bool
     */
    public function isEven()
    {
        return $this->counter % 2 === 0;
    }
    /**
     * Returns the counter.
     * @return int
     */
    public function getCounter()
    {
        return $this->counter;
    }
    /**
     * Returns the count of elements.
     * @return int
     */
    public function count()
    {
        $inner = $this->getInnerIterator();
        if ($inner instanceof \Countable) {
            return $inner->count();
        } else {
            throw new \ECSPrefix20210514\Nette\NotSupportedException('Iterator is not countable.');
        }
    }
    /**
     * Forwards to the next element.
     * @return void
     */
    public function next()
    {
        parent::next();
        if (parent::valid()) {
            $this->counter++;
        }
    }
    /**
     * Rewinds the Iterator.
     * @return void
     */
    public function rewind()
    {
        parent::rewind();
        $this->counter = parent::valid() ? 1 : 0;
    }
    /**
     * Returns the next key.
     * @return mixed
     */
    public function getNextKey()
    {
        return $this->getInnerIterator()->key();
    }
    /**
     * Returns the next element.
     * @return mixed
     */
    public function getNextValue()
    {
        return $this->getInnerIterator()->current();
    }
}
