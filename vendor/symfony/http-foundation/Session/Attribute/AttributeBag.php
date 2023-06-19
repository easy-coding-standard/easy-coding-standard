<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Attribute;

/**
 * This class relates to session attribute storage.
 *
 * @implements \IteratorAggregate<string, mixed>
 */
class AttributeBag implements AttributeBagInterface, \IteratorAggregate, \Countable
{
    /**
     * @var string
     */
    private $name = 'attributes';
    /**
     * @var string
     */
    private $storageKey;
    protected $attributes = [];
    /**
     * @param string $storageKey The key used to store attributes in the session
     */
    public function __construct(string $storageKey = '_sf2_attributes')
    {
        $this->storageKey = $storageKey;
    }
    public function getName() : string
    {
        return $this->name;
    }
    /**
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    /**
     * @return void
     */
    public function initialize(array &$attributes)
    {
        $this->attributes =& $attributes;
    }
    public function getStorageKey() : string
    {
        return $this->storageKey;
    }
    public function has(string $name) : bool
    {
        return \array_key_exists($name, $this->attributes);
    }
    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return \array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }
    /**
     * @return void
     * @param mixed $value
     */
    public function set(string $name, $value)
    {
        $this->attributes[$name] = $value;
    }
    public function all() : array
    {
        return $this->attributes;
    }
    /**
     * @return void
     */
    public function replace(array $attributes)
    {
        $this->attributes = [];
        foreach ($attributes as $key => $value) {
            $this->set($key, $value);
        }
    }
    /**
     * @return mixed
     */
    public function remove(string $name)
    {
        $retval = null;
        if (\array_key_exists($name, $this->attributes)) {
            $retval = $this->attributes[$name];
            unset($this->attributes[$name]);
        }
        return $retval;
    }
    /**
     * @return mixed
     */
    public function clear()
    {
        $return = $this->attributes;
        $this->attributes = [];
        return $return;
    }
    /**
     * Returns an iterator for attributes.
     *
     * @return \ArrayIterator<string, mixed>
     */
    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator($this->attributes);
    }
    /**
     * Returns the number of attributes.
     */
    public function count() : int
    {
        return \count($this->attributes);
    }
}
