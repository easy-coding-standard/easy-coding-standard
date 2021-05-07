<?php

namespace ECSPrefix20210507\Nette\Iterators;

/**
 * Applies the callback to the elements of the inner iterator.
 */
class Mapper extends \IteratorIterator
{
    /** @var callable */
    private $callback;
    /**
     * @param \Traversable $iterator
     */
    public function __construct($iterator, callable $callback)
    {
        parent::__construct($iterator);
        $this->callback = $callback;
    }
    public function current()
    {
        return ($this->callback)(parent::current(), parent::key());
    }
}
