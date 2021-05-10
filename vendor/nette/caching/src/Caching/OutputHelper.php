<?php

namespace ECSPrefix20210510\Nette\Caching;

use ECSPrefix20210510\Nette;
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
    public function __construct(\ECSPrefix20210510\Nette\Caching\Cache $cache, $key)
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
            throw new \ECSPrefix20210510\Nette\InvalidStateException('Output cache has already been saved.');
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
