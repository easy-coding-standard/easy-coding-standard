<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ECSPrefix20210605\Nette\Caching\Storages;

use ECSPrefix20210605\Nette;
use ECSPrefix20210605\Nette\Caching\Cache;
/**
 * Memcached storage using memcached extension.
 */
class MemcachedStorage implements \ECSPrefix20210605\Nette\Caching\Storage, \ECSPrefix20210605\Nette\Caching\BulkReader
{
    use Nette\SmartObject;
    /** @internal cache structure */
    const META_CALLBACKS = 'callbacks', META_DATA = 'data', META_DELTA = 'delta';
    /** @var \Memcached */
    private $memcached;
    /** @var string */
    private $prefix;
    /** @var Journal */
    private $journal;
    /**
     * Checks if Memcached extension is available.
     */
    public static function isAvailable() : bool
    {
        return \extension_loaded('memcached');
    }
    public function __construct(string $host = 'localhost', int $port = 11211, string $prefix = '', \ECSPrefix20210605\Nette\Caching\Storages\Journal $journal = null)
    {
        if (!static::isAvailable()) {
            throw new \ECSPrefix20210605\Nette\NotSupportedException("PHP extension 'memcached' is not loaded.");
        }
        $this->prefix = $prefix;
        $this->journal = $journal;
        $this->memcached = new \Memcached();
        if ($host) {
            $this->addServer($host, $port);
        }
    }
    /**
     * @return void
     */
    public function addServer(string $host = 'localhost', int $port = 11211)
    {
        if (@$this->memcached->addServer($host, $port, 1) === \false) {
            // @ is escalated to exception
            $error = \error_get_last();
            throw new \ECSPrefix20210605\Nette\InvalidStateException("Memcached::addServer(): {$error['message']}.");
        }
    }
    public function getConnection() : \Memcached
    {
        return $this->memcached;
    }
    public function read(string $key)
    {
        $key = \urlencode($this->prefix . $key);
        $meta = $this->memcached->get($key);
        if (!$meta) {
            return null;
        }
        // meta structure:
        // array(
        //     data => stored data
        //     delta => relative (sliding) expiration
        //     callbacks => array of callbacks (function, args)
        // )
        // verify dependencies
        if (!empty($meta[self::META_CALLBACKS]) && !\ECSPrefix20210605\Nette\Caching\Cache::checkCallbacks($meta[self::META_CALLBACKS])) {
            $this->memcached->delete($key, 0);
            return null;
        }
        if (!empty($meta[self::META_DELTA])) {
            $this->memcached->replace($key, $meta, $meta[self::META_DELTA] + \time());
        }
        return $meta[self::META_DATA];
    }
    public function bulkRead(array $keys) : array
    {
        $prefixedKeys = \array_map(function ($key) {
            return \urlencode($this->prefix . $key);
        }, $keys);
        $keys = \array_combine($prefixedKeys, $keys);
        $metas = $this->memcached->getMulti($prefixedKeys);
        $result = [];
        $deleteKeys = [];
        foreach ($metas as $prefixedKey => $meta) {
            if (!empty($meta[self::META_CALLBACKS]) && !\ECSPrefix20210605\Nette\Caching\Cache::checkCallbacks($meta[self::META_CALLBACKS])) {
                $deleteKeys[] = $prefixedKey;
            } else {
                $result[$keys[$prefixedKey]] = $meta[self::META_DATA];
            }
            if (!empty($meta[self::META_DELTA])) {
                $this->memcached->replace($prefixedKey, $meta, $meta[self::META_DELTA] + \time());
            }
        }
        if (!empty($deleteKeys)) {
            $this->memcached->deleteMulti($deleteKeys, 0);
        }
        return $result;
    }
    /**
     * @return void
     */
    public function lock(string $key)
    {
    }
    /**
     * @return void
     */
    public function write(string $key, $data, array $dp)
    {
        if (isset($dp[\ECSPrefix20210605\Nette\Caching\Cache::ITEMS])) {
            throw new \ECSPrefix20210605\Nette\NotSupportedException('Dependent items are not supported by MemcachedStorage.');
        }
        $key = \urlencode($this->prefix . $key);
        $meta = [self::META_DATA => $data];
        $expire = 0;
        if (isset($dp[\ECSPrefix20210605\Nette\Caching\Cache::EXPIRATION])) {
            $expire = (int) $dp[\ECSPrefix20210605\Nette\Caching\Cache::EXPIRATION];
            if (!empty($dp[\ECSPrefix20210605\Nette\Caching\Cache::SLIDING])) {
                $meta[self::META_DELTA] = $expire;
                // sliding time
            }
        }
        if (isset($dp[\ECSPrefix20210605\Nette\Caching\Cache::CALLBACKS])) {
            $meta[self::META_CALLBACKS] = $dp[\ECSPrefix20210605\Nette\Caching\Cache::CALLBACKS];
        }
        if (isset($dp[\ECSPrefix20210605\Nette\Caching\Cache::TAGS]) || isset($dp[\ECSPrefix20210605\Nette\Caching\Cache::PRIORITY])) {
            if (!$this->journal) {
                throw new \ECSPrefix20210605\Nette\InvalidStateException('CacheJournal has not been provided.');
            }
            $this->journal->write($key, $dp);
        }
        $this->memcached->set($key, $meta, $expire);
    }
    /**
     * @return void
     */
    public function remove(string $key)
    {
        $this->memcached->delete(\urlencode($this->prefix . $key), 0);
    }
    /**
     * @return void
     */
    public function clean(array $conditions)
    {
        if (!empty($conditions[\ECSPrefix20210605\Nette\Caching\Cache::ALL])) {
            $this->memcached->flush();
        } elseif ($this->journal) {
            foreach ($this->journal->clean($conditions) as $entry) {
                $this->memcached->delete($entry, 0);
            }
        }
    }
}
