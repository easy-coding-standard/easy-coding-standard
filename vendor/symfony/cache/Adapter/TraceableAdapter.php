<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Cache\Adapter;

use ECSPrefix20210508\Psr\Cache\CacheItemInterface;
use ECSPrefix20210508\Symfony\Component\Cache\CacheItem;
use ECSPrefix20210508\Symfony\Component\Cache\PruneableInterface;
use ECSPrefix20210508\Symfony\Component\Cache\ResettableInterface;
use ECSPrefix20210508\Symfony\Contracts\Cache\CacheInterface;
use ECSPrefix20210508\Symfony\Contracts\Service\ResetInterface;
/**
 * An adapter that collects data about all cache calls.
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 */
class TraceableAdapter implements \ECSPrefix20210508\Symfony\Component\Cache\Adapter\AdapterInterface, \ECSPrefix20210508\Symfony\Contracts\Cache\CacheInterface, \ECSPrefix20210508\Symfony\Component\Cache\PruneableInterface, \ECSPrefix20210508\Symfony\Component\Cache\ResettableInterface
{
    protected $pool;
    private $calls = [];
    public function __construct(\ECSPrefix20210508\Symfony\Component\Cache\Adapter\AdapterInterface $pool)
    {
        $this->pool = $pool;
    }
    /**
     * {@inheritdoc}
     * @param string $key
     * @param float $beta
     */
    public function get($key, callable $callback, $beta = null, array &$metadata = null)
    {
        if (!$this->pool instanceof \ECSPrefix20210508\Symfony\Contracts\Cache\CacheInterface) {
            throw new \BadMethodCallException(\sprintf('Cannot call "%s::get()": this class doesn\'t implement "%s".', \get_debug_type($this->pool), \ECSPrefix20210508\Symfony\Contracts\Cache\CacheInterface::class));
        }
        $isHit = \true;
        $callback = function (\ECSPrefix20210508\Symfony\Component\Cache\CacheItem $item, bool &$save) use($callback, &$isHit) {
            $isHit = $item->isHit();
            return $callback($item, $save);
        };
        $event = $this->start(__FUNCTION__);
        try {
            $value = $this->pool->get($key, $callback, $beta, $metadata);
            $event->result[$key] = \get_debug_type($value);
        } finally {
            $event->end = \microtime(\true);
        }
        if ($isHit) {
            ++$event->hits;
        } else {
            ++$event->misses;
        }
        return $value;
    }
    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        $event = $this->start(__FUNCTION__);
        try {
            $item = $this->pool->getItem($key);
        } finally {
            $event->end = \microtime(\true);
        }
        if ($event->result[$key] = $item->isHit()) {
            ++$event->hits;
        } else {
            ++$event->misses;
        }
        return $item;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function hasItem($key)
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result[$key] = $this->pool->hasItem($key);
        } finally {
            $event->end = \microtime(\true);
        }
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItem($key)
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result[$key] = $this->pool->deleteItem($key);
        } finally {
            $event->end = \microtime(\true);
        }
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function save(\ECSPrefix20210508\Psr\Cache\CacheItemInterface $item)
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result[$item->getKey()] = $this->pool->save($item);
        } finally {
            $event->end = \microtime(\true);
        }
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function saveDeferred(\ECSPrefix20210508\Psr\Cache\CacheItemInterface $item)
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result[$item->getKey()] = $this->pool->saveDeferred($item);
        } finally {
            $event->end = \microtime(\true);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        $event = $this->start(__FUNCTION__);
        try {
            $result = $this->pool->getItems($keys);
        } finally {
            $event->end = \microtime(\true);
        }
        $f = function () use($result, $event) {
            $event->result = [];
            foreach ($result as $key => $item) {
                if ($event->result[$key] = $item->isHit()) {
                    ++$event->hits;
                } else {
                    ++$event->misses;
                }
                (yield $key => $item);
            }
        };
        return $f();
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     * @param string $prefix
     */
    public function clear($prefix = '')
    {
        $event = $this->start(__FUNCTION__);
        try {
            if ($this->pool instanceof \ECSPrefix20210508\Symfony\Component\Cache\Adapter\AdapterInterface) {
                return $event->result = $this->pool->clear($prefix);
            }
            return $event->result = $this->pool->clear();
        } finally {
            $event->end = \microtime(\true);
        }
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItems(array $keys)
    {
        $event = $this->start(__FUNCTION__);
        $event->result['keys'] = $keys;
        try {
            return $event->result['result'] = $this->pool->deleteItems($keys);
        } finally {
            $event->end = \microtime(\true);
        }
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function commit()
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result = $this->pool->commit();
        } finally {
            $event->end = \microtime(\true);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function prune()
    {
        if (!$this->pool instanceof \ECSPrefix20210508\Symfony\Component\Cache\PruneableInterface) {
            return \false;
        }
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result = $this->pool->prune();
        } finally {
            $event->end = \microtime(\true);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        if ($this->pool instanceof \ECSPrefix20210508\Symfony\Contracts\Service\ResetInterface) {
            $this->pool->reset();
        }
        $this->clearCalls();
    }
    /**
     * {@inheritdoc}
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        $event = $this->start(__FUNCTION__);
        try {
            return $event->result[$key] = $this->pool->deleteItem($key);
        } finally {
            $event->end = \microtime(\true);
        }
    }
    public function getCalls()
    {
        return $this->calls;
    }
    public function clearCalls()
    {
        $this->calls = [];
    }
    protected function start($name)
    {
        $this->calls[] = $event = new \ECSPrefix20210508\Symfony\Component\Cache\Adapter\TraceableAdapterEvent();
        $event->name = $name;
        $event->start = \microtime(\true);
        return $event;
    }
}
class TraceableAdapterEvent
{
    public $name;
    public $start;
    public $end;
    public $result;
    public $hits = 0;
    public $misses = 0;
}
