<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ECSPrefix20210526\Nette\Caching;

use ECSPrefix20210526\Nette;
/**
 * Output caching helper.
 */
class OutputHelper
{
    use Nette\SmartObject;
    /** @var array */
    public $dependencies = [];
    /** @var Cache|null */
    private $cache;
    /** @var string */
    private $key;
    public function __construct(\ECSPrefix20210526\Nette\Caching\Cache $cache, $key)
    {
        $this->cache = $cache;
        $this->key = $key;
        \ob_start();
    }
    /**
     * Stops and saves the cache.
     * @return void
     */
    public function end(array $dependencies = [])
    {
        if ($this->cache === null) {
            throw new \ECSPrefix20210526\Nette\InvalidStateException('Output cache has already been saved.');
        }
        $this->cache->save($this->key, \ob_get_flush(), $dependencies + $this->dependencies);
        $this->cache = null;
    }
    /**
     * Stops and throws away the output.
     * @return void
     */
    public function rollback()
    {
        \ob_end_flush();
        $this->cache = null;
    }
}
