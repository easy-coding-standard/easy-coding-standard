<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ECSPrefix20210519\Nette\Caching;

/**
 * Cache storage.
 */
interface Storage
{
    /**
     * Read from cache.
     * @return mixed
     */
    function read(string $key);
    /**
     * Prevents item reading and writing. Lock is released by write() or remove().
     * @return void
     */
    function lock(string $key);
    /**
     * Writes item into the cache.
     * @return void
     */
    function write(string $key, $data, array $dependencies);
    /**
     * Removes item from the cache.
     * @return void
     */
    function remove(string $key);
    /**
     * Removes items from the cache by conditions.
     * @return void
     */
    function clean(array $conditions);
}
\class_exists(\ECSPrefix20210519\Nette\Caching\IStorage::class);
