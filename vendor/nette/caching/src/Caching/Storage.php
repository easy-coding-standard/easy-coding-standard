<?php

namespace ECSPrefix20210516\Nette\Caching;

/**
 * Cache storage.
 */
interface Storage
{
    /**
     * Read from cache.
     * @return mixed
     * @param string $key
     */
    function read($key);
    /**
     * Prevents item reading and writing. Lock is released by write() or remove().
     * @return void
     * @param string $key
     */
    function lock($key);
    /**
     * Writes item into the cache.
     * @return void
     * @param string $key
     */
    function write($key, $data, array $dependencies);
    /**
     * Removes item from the cache.
     * @return void
     * @param string $key
     */
    function remove($key);
    /**
     * Removes items from the cache by conditions.
     * @return void
     */
    function clean(array $conditions);
}
\class_exists(\ECSPrefix20210516\Nette\Caching\IStorage::class);
