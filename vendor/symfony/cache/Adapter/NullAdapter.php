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
use ECSPrefix20210508\Symfony\Contracts\Cache\CacheInterface;
/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class NullAdapter implements \ECSPrefix20210508\Symfony\Component\Cache\Adapter\AdapterInterface, \ECSPrefix20210508\Symfony\Contracts\Cache\CacheInterface
{
    private $createCacheItem;
    public function __construct()
    {
        $this->createCacheItem = \Closure::bind(function ($key) {
            $item = new \ECSPrefix20210508\Symfony\Component\Cache\CacheItem();
            $item->key = $key;
            $item->isHit = \false;
            return $item;
        }, $this, \ECSPrefix20210508\Symfony\Component\Cache\CacheItem::class);
    }
    /**
     * {@inheritdoc}
     * @param string $key
     * @param float $beta
     */
    public function get($key, callable $callback, $beta = null, array &$metadata = null)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        $save = \true;
        return $callback(($this->createCacheItem)($key), $save);
    }
    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        $f = $this->createCacheItem;
        return $f($key);
    }
    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        return $this->generateItems($keys);
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function hasItem($key)
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     * @param string $prefix
     */
    public function clear($prefix = '')
    {
        if (\is_object($prefix)) {
            $prefix = (string) $prefix;
        }
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItem($key)
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItems(array $keys)
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function save(\ECSPrefix20210508\Psr\Cache\CacheItemInterface $item)
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function saveDeferred(\ECSPrefix20210508\Psr\Cache\CacheItemInterface $item)
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function commit()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        if (\is_object($key)) {
            $key = (string) $key;
        }
        return $this->deleteItem($key);
    }
    private function generateItems(array $keys)
    {
        $f = $this->createCacheItem;
        foreach ($keys as $key) {
            (yield $key => $f($key));
        }
    }
}
