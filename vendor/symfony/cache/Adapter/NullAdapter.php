<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\Cache\Adapter;

use ConfigTransformer20210601\Psr\Cache\CacheItemInterface;
use ConfigTransformer20210601\Symfony\Component\Cache\CacheItem;
use ConfigTransformer20210601\Symfony\Contracts\Cache\CacheInterface;
/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class NullAdapter implements \ConfigTransformer20210601\Symfony\Component\Cache\Adapter\AdapterInterface, \ConfigTransformer20210601\Symfony\Contracts\Cache\CacheInterface
{
    private static $createCacheItem;
    public function __construct()
    {
        self::$createCacheItem ?? (self::$createCacheItem = \Closure::bind(static function ($key) {
            $item = new \ConfigTransformer20210601\Symfony\Component\Cache\CacheItem();
            $item->key = $key;
            $item->isHit = \false;
            return $item;
        }, null, \ConfigTransformer20210601\Symfony\Component\Cache\CacheItem::class));
    }
    /**
     * {@inheritdoc}
     */
    public function get(string $key, callable $callback, float $beta = null, array &$metadata = null)
    {
        $save = \true;
        return $callback((self::$createCacheItem)($key), $save);
    }
    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        return (self::$createCacheItem)($key);
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
    public function save(\ConfigTransformer20210601\Psr\Cache\CacheItemInterface $item)
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function saveDeferred(\ConfigTransformer20210601\Psr\Cache\CacheItemInterface $item)
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
     */
    public function delete(string $key) : bool
    {
        return $this->deleteItem($key);
    }
    private function generateItems(array $keys)
    {
        $f = self::$createCacheItem;
        foreach ($keys as $key) {
            (yield $key => $f($key));
        }
    }
}
