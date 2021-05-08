<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Cache\Messenger;

use ECSPrefix20210508\Symfony\Component\Cache\Adapter\AdapterInterface;
use ECSPrefix20210508\Symfony\Component\Cache\CacheItem;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ReverseContainer;
/**
 * Conveys a cached value that needs to be computed.
 */
final class EarlyExpirationMessage
{
    private $item;
    private $pool;
    private $callback;
    /**
     * @return $this|null
     */
    public static function create(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ReverseContainer $reverseContainer, callable $callback, \ECSPrefix20210508\Symfony\Component\Cache\CacheItem $item, \ECSPrefix20210508\Symfony\Component\Cache\Adapter\AdapterInterface $pool)
    {
        try {
            $item = clone $item;
            $item->set(null);
        } catch (\Exception $e) {
            return null;
        }
        $pool = $reverseContainer->getId($pool);
        if (\is_object($callback)) {
            if (null === ($id = $reverseContainer->getId($callback))) {
                return null;
            }
            $callback = '@' . $id;
        } elseif (!\is_array($callback)) {
            $callback = (string) $callback;
        } elseif (!\is_object($callback[0])) {
            $callback = [(string) $callback[0], (string) $callback[1]];
        } else {
            if (null === ($id = $reverseContainer->getId($callback[0]))) {
                return null;
            }
            $callback = ['@' . $id, (string) $callback[1]];
        }
        return new self($item, $pool, $callback);
    }
    /**
     * @return \Symfony\Component\Cache\CacheItem
     */
    public function getItem()
    {
        return $this->item;
    }
    /**
     * @return string
     */
    public function getPool()
    {
        return $this->pool;
    }
    public function getCallback()
    {
        return $this->callback;
    }
    /**
     * @return \Symfony\Component\Cache\Adapter\AdapterInterface
     */
    public function findPool(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ReverseContainer $reverseContainer)
    {
        return $reverseContainer->getService($this->pool);
    }
    /**
     * @return callable
     */
    public function findCallback(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ReverseContainer $reverseContainer)
    {
        if (\is_string($callback = $this->callback)) {
            return '@' === $callback[0] ? $reverseContainer->getService(\substr($callback, 1)) : $callback;
        }
        if ('@' === $callback[0][0]) {
            $callback[0] = $reverseContainer->getService(\substr($callback[0], 1));
        }
        return $callback;
    }
    /**
     * @param string $pool
     */
    private function __construct(\ECSPrefix20210508\Symfony\Component\Cache\CacheItem $item, $pool, $callback)
    {
        if (\is_object($pool)) {
            $pool = (string) $pool;
        }
        $this->item = $item;
        $this->pool = $pool;
        $this->callback = $callback;
    }
}
