<?php

namespace ECSPrefix20210515\Nette\Iterators;

/**
 * Applies the callback to the elements of the inner iterator.
 */
class Mapper extends \IteratorIterator
{
    /** @var callable */
    private $callback;
    public function __construct(\Traversable $iterator, callable $callback)
    {
        parent::__construct($iterator);
        $this->callback = $callback;
    }
    public function current()
    {
        return ($this->callback)(parent::current(), parent::key());
    }
}
