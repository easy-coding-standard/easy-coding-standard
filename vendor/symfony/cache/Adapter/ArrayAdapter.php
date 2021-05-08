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
use ECSPrefix20210508\Psr\Log\LoggerAwareInterface;
use ECSPrefix20210508\Psr\Log\LoggerAwareTrait;
use ECSPrefix20210508\Symfony\Component\Cache\CacheItem;
use ECSPrefix20210508\Symfony\Component\Cache\Exception\InvalidArgumentException;
use ECSPrefix20210508\Symfony\Component\Cache\ResettableInterface;
use ECSPrefix20210508\Symfony\Contracts\Cache\CacheInterface;
/**
 * An in-memory cache storage.
 *
 * Acts as a least-recently-used (LRU) storage when configured with a maximum number of items.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ArrayAdapter implements \ECSPrefix20210508\Symfony\Component\Cache\Adapter\AdapterInterface, \ECSPrefix20210508\Symfony\Contracts\Cache\CacheInterface, \ECSPrefix20210508\Psr\Log\LoggerAwareInterface, \ECSPrefix20210508\Symfony\Component\Cache\ResettableInterface
{
    use LoggerAwareTrait;
    private $storeSerialized;
    private $values = [];
    private $expiries = [];
    private $createCacheItem;
    private $defaultLifetime;
    private $maxLifetime;
    private $maxItems;
    /**
     * @param bool $storeSerialized Disabling serialization can lead to cache corruptions when storing mutable values but increases performance otherwise
     * @param int $defaultLifetime
     * @param float $maxLifetime
     * @param int $maxItems
     */
    public function __construct($defaultLifetime = 0, $storeSerialized = \true, $maxLifetime = 0, $maxItems = 0)
    {
        $defaultLifetime = (int) $defaultLifetime;
        $storeSerialized = (bool) $storeSerialized;
        $maxLifetime = (double) $maxLifetime;
        $maxItems = (int) $maxItems;
        if (0 > $maxLifetime) {
            throw new \ECSPrefix20210508\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Argument $maxLifetime must be positive, %F passed.', $maxLifetime));
        }
        if (0 > $maxItems) {
            throw new \ECSPrefix20210508\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Argument $maxItems must be a positive integer, %d passed.', $maxItems));
        }
        $this->defaultLifetime = $defaultLifetime;
        $this->storeSerialized = $storeSerialized;
        $this->maxLifetime = $maxLifetime;
        $this->maxItems = $maxItems;
        $this->createCacheItem = \Closure::bind(static function ($key, $value, $isHit) {
            $item = new \ECSPrefix20210508\Symfony\Component\Cache\CacheItem();
            $item->key = $key;
            $item->value = $value;
            $item->isHit = $isHit;
            return $item;
        }, null, \ECSPrefix20210508\Symfony\Component\Cache\CacheItem::class);
    }
    /**
     * {@inheritdoc}
     * @param string $key
     * @param float $beta
     */
    public function get($key, callable $callback, $beta = null, array &$metadata = null)
    {
        $key = (string) $key;
        $item = $this->getItem($key);
        $metadata = $item->getMetadata();
        // ArrayAdapter works in memory, we don't care about stampede protection
        if (\INF === $beta || !$item->isHit()) {
            $save = \true;
            $this->save($item->set($callback($item, $save)));
        }
        return $item->get();
    }
    /**
     * {@inheritdoc}
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        $key = (string) $key;
        return $this->deleteItem($key);
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function hasItem($key)
    {
        if (\is_string($key) && isset($this->expiries[$key]) && $this->expiries[$key] > \microtime(\true)) {
            if ($this->maxItems) {
                // Move the item last in the storage
                $value = $this->values[$key];
                unset($this->values[$key]);
                $this->values[$key] = $value;
            }
            return \true;
        }
        \ECSPrefix20210508\Symfony\Component\Cache\CacheItem::validateKey($key);
        return isset($this->expiries[$key]) && !$this->deleteItem($key);
    }
    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        if (!($isHit = $this->hasItem($key))) {
            $value = null;
            if (!$this->maxItems) {
                // Track misses in non-LRU mode only
                $this->values[$key] = null;
            }
        } else {
            $value = $this->storeSerialized ? $this->unfreeze($key, $isHit) : $this->values[$key];
        }
        $f = $this->createCacheItem;
        return $f($key, $value, $isHit);
    }
    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        foreach ($keys as $key) {
            if (!\is_string($key) || !isset($this->expiries[$key])) {
                \ECSPrefix20210508\Symfony\Component\Cache\CacheItem::validateKey($key);
            }
        }
        return $this->generateItems($keys, \microtime(\true), $this->createCacheItem);
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItem($key)
    {
        if (!\is_string($key) || !isset($this->expiries[$key])) {
            \ECSPrefix20210508\Symfony\Component\Cache\CacheItem::validateKey($key);
        }
        unset($this->values[$key], $this->expiries[$key]);
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function save(\ECSPrefix20210508\Psr\Cache\CacheItemInterface $item)
    {
        if (!$item instanceof \ECSPrefix20210508\Symfony\Component\Cache\CacheItem) {
            return \false;
        }
        $item = (array) $item;
        $key = $item["\0*\0key"];
        $value = $item["\0*\0value"];
        $expiry = $item["\0*\0expiry"];
        $now = \microtime(\true);
        if (0 === $expiry) {
            $expiry = \PHP_INT_MAX;
        }
        if (null !== $expiry && $expiry <= $now) {
            $this->deleteItem($key);
            return \true;
        }
        if ($this->storeSerialized && null === ($value = $this->freeze($value, $key))) {
            return \false;
        }
        if (null === $expiry && 0 < $this->defaultLifetime) {
            $expiry = $this->defaultLifetime;
            $expiry = $now + ($expiry > ($this->maxLifetime ?: $expiry) ? $this->maxLifetime : $expiry);
        } elseif ($this->maxLifetime && (null === $expiry || $expiry > $now + $this->maxLifetime)) {
            $expiry = $now + $this->maxLifetime;
        }
        if ($this->maxItems) {
            unset($this->values[$key]);
            // Iterate items and vacuum expired ones while we are at it
            foreach ($this->values as $k => $v) {
                if ($this->expiries[$k] > $now && \count($this->values) < $this->maxItems) {
                    break;
                }
                unset($this->values[$k], $this->expiries[$k]);
            }
        }
        $this->values[$key] = $value;
        $this->expiries[$key] = null !== $expiry ? $expiry : \PHP_INT_MAX;
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function saveDeferred(\ECSPrefix20210508\Psr\Cache\CacheItemInterface $item)
    {
        return $this->save($item);
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
     *
     * @return bool
     * @param string $prefix
     */
    public function clear($prefix = '')
    {
        $prefix = (string) $prefix;
        if ('' !== $prefix) {
            $now = \microtime(\true);
            foreach ($this->values as $key => $value) {
                if (!isset($this->expiries[$key]) || $this->expiries[$key] <= $now || 0 === \strpos($key, $prefix)) {
                    unset($this->values[$key], $this->expiries[$key]);
                }
            }
            if ($this->values) {
                return \true;
            }
        }
        $this->values = $this->expiries = [];
        return \true;
    }
    /**
     * Returns all cached values, with cache miss as null.
     *
     * @return array
     */
    public function getValues()
    {
        if (!$this->storeSerialized) {
            return $this->values;
        }
        $values = $this->values;
        foreach ($values as $k => $v) {
            if (null === $v || 'N;' === $v) {
                continue;
            }
            if (!\is_string($v) || !isset($v[2]) || ':' !== $v[1]) {
                $values[$k] = \serialize($v);
            }
        }
        return $values;
    }
    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->clear();
    }
    private function generateItems(array $keys, $now, $f)
    {
        foreach ($keys as $i => $key) {
            if (!($isHit = isset($this->expiries[$key]) && ($this->expiries[$key] > $now || !$this->deleteItem($key)))) {
                $value = null;
                if (!$this->maxItems) {
                    // Track misses in non-LRU mode only
                    $this->values[$key] = null;
                }
            } else {
                if ($this->maxItems) {
                    // Move the item last in the storage
                    $value = $this->values[$key];
                    unset($this->values[$key]);
                    $this->values[$key] = $value;
                }
                $value = $this->storeSerialized ? $this->unfreeze($key, $isHit) : $this->values[$key];
            }
            unset($keys[$i]);
            (yield $key => $f($key, $value, $isHit));
        }
        foreach ($keys as $key) {
            (yield $key => $f($key, null, \false));
        }
    }
    private function freeze($value, $key)
    {
        if (null === $value) {
            return 'N;';
        }
        if (\is_string($value)) {
            // Serialize strings if they could be confused with serialized objects or arrays
            if ('N;' === $value || isset($value[2]) && ':' === $value[1]) {
                return \serialize($value);
            }
        } elseif (!\is_scalar($value)) {
            try {
                $serialized = \serialize($value);
            } catch (\Exception $e) {
                unset($this->values[$key]);
                $type = \get_debug_type($value);
                $message = \sprintf('Failed to save key "{key}" of type %s: %s', $type, $e->getMessage());
                \ECSPrefix20210508\Symfony\Component\Cache\CacheItem::log($this->logger, $message, ['key' => $key, 'exception' => $e, 'cache-adapter' => \get_debug_type($this)]);
                return;
            }
            // Keep value serialized if it contains any objects or any internal references
            if ('C' === $serialized[0] || 'O' === $serialized[0] || \preg_match('/;[OCRr]:[1-9]/', $serialized)) {
                return $serialized;
            }
        }
        return $value;
    }
    /**
     * @param string $key
     * @param bool $isHit
     */
    private function unfreeze($key, &$isHit)
    {
        $key = (string) $key;
        $isHit = (bool) $isHit;
        if ('N;' === ($value = $this->values[$key])) {
            return null;
        }
        if (\is_string($value) && isset($value[2]) && ':' === $value[1]) {
            try {
                $value = \unserialize($value);
            } catch (\Exception $e) {
                \ECSPrefix20210508\Symfony\Component\Cache\CacheItem::log($this->logger, 'Failed to unserialize key "{key}": ' . $e->getMessage(), ['key' => $key, 'exception' => $e, 'cache-adapter' => \get_debug_type($this)]);
                $value = \false;
            }
            if (\false === $value) {
                $value = null;
                $isHit = \false;
                if (!$this->maxItems) {
                    $this->values[$key] = null;
                }
            }
        }
        return $value;
    }
}
