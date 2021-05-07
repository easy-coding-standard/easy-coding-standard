<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Cache\Traits;

use ECSPrefix20210507\Psr\Log\LoggerInterface;
use ECSPrefix20210507\Symfony\Component\Cache\Adapter\AdapterInterface;
use ECSPrefix20210507\Symfony\Component\Cache\CacheItem;
use ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException;
use ECSPrefix20210507\Symfony\Component\Cache\LockRegistry;
use ECSPrefix20210507\Symfony\Contracts\Cache\CacheInterface;
use ECSPrefix20210507\Symfony\Contracts\Cache\CacheTrait;
use ECSPrefix20210507\Symfony\Contracts\Cache\ItemInterface;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
trait ContractsTrait
{
    use CacheTrait {
        doGet as private contractsGet;
    }
    private $callbackWrapper = [\ECSPrefix20210507\Symfony\Component\Cache\LockRegistry::class, 'compute'];
    private $computing = [];
    /**
     * Wraps the callback passed to ->get() in a callable.
     *
     * @return callable the previous callback wrapper
     * @param callable|null $callbackWrapper
     */
    public function setCallbackWrapper($callbackWrapper)
    {
        $previousWrapper = $this->callbackWrapper;
        $this->callbackWrapper = isset($callbackWrapper) ? $callbackWrapper : function (callable $callback, \ECSPrefix20210507\Symfony\Contracts\Cache\ItemInterface $item, bool &$save, \ECSPrefix20210507\Symfony\Contracts\Cache\CacheInterface $pool, \Closure $setMetadata, $logger) {
            return $callback($item, $save);
        };
        return $previousWrapper;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Cache\Adapter\AdapterInterface $pool
     * @param float|null $beta
     * @param string $key
     */
    private function doGet($pool, $key, callable $callback, $beta, array &$metadata = null)
    {
        if (0 > ($beta = isset($beta) ? $beta : 1.0)) {
            throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Argument "$beta" provided to "%s::get()" must be a positive number, %f given.', static::class, $beta));
        }
        static $setMetadata;
        $setMetadata = isset($setMetadata) ? $setMetadata : \Closure::bind(static function (\ECSPrefix20210507\Symfony\Component\Cache\CacheItem $item, float $startTime, &$metadata) {
            if ($item->expiry > ($endTime = \microtime(\true))) {
                $item->newMetadata[\ECSPrefix20210507\Symfony\Component\Cache\CacheItem::METADATA_EXPIRY] = $metadata[\ECSPrefix20210507\Symfony\Component\Cache\CacheItem::METADATA_EXPIRY] = $item->expiry;
                $item->newMetadata[\ECSPrefix20210507\Symfony\Component\Cache\CacheItem::METADATA_CTIME] = $metadata[\ECSPrefix20210507\Symfony\Component\Cache\CacheItem::METADATA_CTIME] = (int) \ceil(1000 * ($endTime - $startTime));
            } else {
                unset($metadata[\ECSPrefix20210507\Symfony\Component\Cache\CacheItem::METADATA_EXPIRY], $metadata[\ECSPrefix20210507\Symfony\Component\Cache\CacheItem::METADATA_CTIME]);
            }
        }, null, \ECSPrefix20210507\Symfony\Component\Cache\CacheItem::class);
        return $this->contractsGet($pool, $key, function (\ECSPrefix20210507\Symfony\Component\Cache\CacheItem $item, bool &$save) use($pool, $callback, $setMetadata, &$metadata, $key) {
            // don't wrap nor save recursive calls
            if (isset($this->computing[$key])) {
                $value = $callback($item, $save);
                $save = \false;
                return $value;
            }
            $this->computing[$key] = $key;
            $startTime = \microtime(\true);
            try {
                $value = ($this->callbackWrapper)($callback, $item, $save, $pool, function (\ECSPrefix20210507\Symfony\Component\Cache\CacheItem $item) use($setMetadata, $startTime, &$metadata) {
                    $setMetadata($item, $startTime, $metadata);
                }, isset($this->logger) ? $this->logger : null);
                $setMetadata($item, $startTime, $metadata);
                return $value;
            } finally {
                unset($this->computing[$key]);
            }
        }, $beta, $metadata, isset($this->logger) ? $this->logger : null);
    }
}
