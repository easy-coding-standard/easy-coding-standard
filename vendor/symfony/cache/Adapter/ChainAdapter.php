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
use ConfigTransformer20210601\Psr\Cache\CacheItemPoolInterface;
use ConfigTransformer20210601\Symfony\Component\Cache\CacheItem;
use ConfigTransformer20210601\Symfony\Component\Cache\Exception\InvalidArgumentException;
use ConfigTransformer20210601\Symfony\Component\Cache\PruneableInterface;
use ConfigTransformer20210601\Symfony\Component\Cache\ResettableInterface;
use ConfigTransformer20210601\Symfony\Component\Cache\Traits\ContractsTrait;
use ConfigTransformer20210601\Symfony\Contracts\Cache\CacheInterface;
use ConfigTransformer20210601\Symfony\Contracts\Service\ResetInterface;
/**
 * Chains several adapters together.
 *
 * Cached items are fetched from the first adapter having them in its data store.
 * They are saved and deleted in all adapters at once.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ChainAdapter implements \ConfigTransformer20210601\Symfony\Component\Cache\Adapter\AdapterInterface, \ConfigTransformer20210601\Symfony\Contracts\Cache\CacheInterface, \ConfigTransformer20210601\Symfony\Component\Cache\PruneableInterface, \ConfigTransformer20210601\Symfony\Component\Cache\ResettableInterface
{
    use ContractsTrait;
    private $adapters = [];
    private $adapterCount;
    private $defaultLifetime;
    private static $syncItem;
    /**
     * @param CacheItemPoolInterface[] $adapters        The ordered list of adapters used to fetch cached items
     * @param int                      $defaultLifetime The default lifetime of items propagated from lower adapters to upper ones
     */
    public function __construct(array $adapters, int $defaultLifetime = 0)
    {
        if (!$adapters) {
            throw new \ConfigTransformer20210601\Symfony\Component\Cache\Exception\InvalidArgumentException('At least one adapter must be specified.');
        }
        foreach ($adapters as $adapter) {
            if (!$adapter instanceof \ConfigTransformer20210601\Psr\Cache\CacheItemPoolInterface) {
                throw new \ConfigTransformer20210601\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('The class "%s" does not implement the "%s" interface.', \get_debug_type($adapter), \ConfigTransformer20210601\Psr\Cache\CacheItemPoolInterface::class));
            }
            if (\in_array(\PHP_SAPI, ['cli', 'phpdbg'], \true) && $adapter instanceof \ConfigTransformer20210601\Symfony\Component\Cache\Adapter\ApcuAdapter && !\filter_var(\ini_get('apc.enable_cli'), \FILTER_VALIDATE_BOOLEAN)) {
                continue;
                // skip putting APCu in the chain when the backend is disabled
            }
            if ($adapter instanceof \ConfigTransformer20210601\Symfony\Component\Cache\Adapter\AdapterInterface) {
                $this->adapters[] = $adapter;
            } else {
                $this->adapters[] = new \ConfigTransformer20210601\Symfony\Component\Cache\Adapter\ProxyAdapter($adapter);
            }
        }
        $this->adapterCount = \count($this->adapters);
        $this->defaultLifetime = $defaultLifetime;
        self::$syncItem ?? (self::$syncItem = \Closure::bind(static function ($sourceItem, $item, $defaultLifetime, $sourceMetadata = null) {
            $sourceItem->isTaggable = \false;
            $sourceMetadata = $sourceMetadata ?? $sourceItem->metadata;
            unset($sourceMetadata[\ConfigTransformer20210601\Symfony\Component\Cache\CacheItem::METADATA_TAGS]);
            $item->value = $sourceItem->value;
            $item->isHit = $sourceItem->isHit;
            $item->metadata = $item->newMetadata = $sourceItem->metadata = $sourceMetadata;
            if (isset($item->metadata[\ConfigTransformer20210601\Symfony\Component\Cache\CacheItem::METADATA_EXPIRY])) {
                $item->expiresAt(\DateTime::createFromFormat('U.u', \sprintf('%.6F', $item->metadata[\ConfigTransformer20210601\Symfony\Component\Cache\CacheItem::METADATA_EXPIRY])));
            } elseif (0 < $defaultLifetime) {
                $item->expiresAfter($defaultLifetime);
            }
            return $item;
        }, null, \ConfigTransformer20210601\Symfony\Component\Cache\CacheItem::class));
    }
    /**
     * {@inheritdoc}
     */
    public function get(string $key, callable $callback, float $beta = null, array &$metadata = null)
    {
        $lastItem = null;
        $i = 0;
        $wrap = function (\ConfigTransformer20210601\Symfony\Component\Cache\CacheItem $item = null) use($key, $callback, $beta, &$wrap, &$i, &$lastItem, &$metadata) {
            $adapter = $this->adapters[$i];
            if (isset($this->adapters[++$i])) {
                $callback = $wrap;
                $beta = \INF === $beta ? \INF : 0;
            }
            if ($adapter instanceof \ConfigTransformer20210601\Symfony\Contracts\Cache\CacheInterface) {
                $value = $adapter->get($key, $callback, $beta, $metadata);
            } else {
                $value = $this->doGet($adapter, $key, $callback, $beta, $metadata);
            }
            if (null !== $item) {
                (self::$syncItem)($lastItem = $lastItem ?? $item, $item, $this->defaultLifetime, $metadata);
            }
            return $value;
        };
        return $wrap();
    }
    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        $syncItem = self::$syncItem;
        $misses = [];
        foreach ($this->adapters as $i => $adapter) {
            $item = $adapter->getItem($key);
            if ($item->isHit()) {
                while (0 <= --$i) {
                    $this->adapters[$i]->save($syncItem($item, $misses[$i], $this->defaultLifetime));
                }
                return $item;
            }
            $misses[$i] = $item;
        }
        return $item;
    }
    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        return $this->generateItems($this->adapters[0]->getItems($keys), 0);
    }
    /**
     * @param mixed[] $items
     */
    private function generateItems($items, int $adapterIndex)
    {
        $missing = [];
        $misses = [];
        $nextAdapterIndex = $adapterIndex + 1;
        $nextAdapter = $this->adapters[$nextAdapterIndex] ?? null;
        foreach ($items as $k => $item) {
            if (!$nextAdapter || $item->isHit()) {
                (yield $k => $item);
            } else {
                $missing[] = $k;
                $misses[$k] = $item;
            }
        }
        if ($missing) {
            $syncItem = self::$syncItem;
            $adapter = $this->adapters[$adapterIndex];
            $items = $this->generateItems($nextAdapter->getItems($missing), $nextAdapterIndex);
            foreach ($items as $k => $item) {
                if ($item->isHit()) {
                    $adapter->save($syncItem($item, $misses[$k], $this->defaultLifetime));
                }
                (yield $k => $item);
            }
        }
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function hasItem($key)
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->hasItem($key)) {
                return \true;
            }
        }
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
        $cleared = \true;
        $i = $this->adapterCount;
        while ($i--) {
            if ($this->adapters[$i] instanceof \ConfigTransformer20210601\Symfony\Component\Cache\Adapter\AdapterInterface) {
                $cleared = $this->adapters[$i]->clear($prefix) && $cleared;
            } else {
                $cleared = $this->adapters[$i]->clear() && $cleared;
            }
        }
        return $cleared;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItem($key)
    {
        $deleted = \true;
        $i = $this->adapterCount;
        while ($i--) {
            $deleted = $this->adapters[$i]->deleteItem($key) && $deleted;
        }
        return $deleted;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItems(array $keys)
    {
        $deleted = \true;
        $i = $this->adapterCount;
        while ($i--) {
            $deleted = $this->adapters[$i]->deleteItems($keys) && $deleted;
        }
        return $deleted;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function save(\ConfigTransformer20210601\Psr\Cache\CacheItemInterface $item)
    {
        $saved = \true;
        $i = $this->adapterCount;
        while ($i--) {
            $saved = $this->adapters[$i]->save($item) && $saved;
        }
        return $saved;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function saveDeferred(\ConfigTransformer20210601\Psr\Cache\CacheItemInterface $item)
    {
        $saved = \true;
        $i = $this->adapterCount;
        while ($i--) {
            $saved = $this->adapters[$i]->saveDeferred($item) && $saved;
        }
        return $saved;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function commit()
    {
        $committed = \true;
        $i = $this->adapterCount;
        while ($i--) {
            $committed = $this->adapters[$i]->commit() && $committed;
        }
        return $committed;
    }
    /**
     * {@inheritdoc}
     */
    public function prune()
    {
        $pruned = \true;
        foreach ($this->adapters as $adapter) {
            if ($adapter instanceof \ConfigTransformer20210601\Symfony\Component\Cache\PruneableInterface) {
                $pruned = $adapter->prune() && $pruned;
            }
        }
        return $pruned;
    }
    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter instanceof \ConfigTransformer20210601\Symfony\Contracts\Service\ResetInterface) {
                $adapter->reset();
            }
        }
    }
}
