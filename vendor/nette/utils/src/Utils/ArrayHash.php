<?php

namespace ECSPrefix20210516\Nette\Utils;

use ECSPrefix20210516\Nette;
/**
 * Provides objects to work as array.
 */
class ArrayHash extends \stdClass implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Transforms array to ArrayHash.
     * @return static
     * @param bool $recursive
     */
    public static function from(array $array, $recursive = \true)
    {
        $recursive = (bool) $recursive;
        $obj = new static();
        foreach ($array as $key => $value) {
            $obj->{$key} = $recursive && \is_array($value) ? static::from($value, \true) : $value;
        }
        return $obj;
    }
    /**
     * Returns an iterator over all items.
     * @return \RecursiveArrayIterator
     */
    public function getIterator()
    {
        return new \RecursiveArrayIterator((array) $this);
    }
    /**
     * Returns items count.
     * @return int
     */
    public function count()
    {
        return \count((array) $this);
    }
    /**
     * Replaces or appends a item.
     * @param  string|int  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (!\is_scalar($key)) {
            // prevents null
            throw new \ECSPrefix20210516\Nette\InvalidArgumentException(\sprintf('Key must be either a string or an integer, %s given.', \gettype($key)));
        }
        $this->{$key} = $value;
    }
    /**
     * Returns a item.
     * @param  string|int  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->{$key};
    }
    /**
     * Determines whether a item exists.
     * @param  string|int  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->{$key});
    }
    /**
     * Removes the element from this list.
     * @param  string|int  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->{$key});
    }
}
