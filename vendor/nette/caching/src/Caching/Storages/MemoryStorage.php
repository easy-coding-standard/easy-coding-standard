<?php

namespace ECSPrefix20210517\Nette\Caching\Storages;

use ECSPrefix20210517\Nette;
/**
 * Memory cache storage.
 */
class MemoryStorage implements \ECSPrefix20210517\Nette\Caching\Storage
{
    use Nette\SmartObject;
    /** @var array */
    private $data = [];
    /**
     * @param string $key
     */
    public function read($key)
    {
        $key = (string) $key;
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
    /**
     * @return void
     * @param string $key
     */
    public function lock($key)
    {
    }
    /**
     * @return void
     * @param string $key
     */
    public function write($key, $data, array $dependencies)
    {
        $key = (string) $key;
        $this->data[$key] = $data;
    }
    /**
     * @return void
     * @param string $key
     */
    public function remove($key)
    {
        $key = (string) $key;
        unset($this->data[$key]);
    }
    /**
     * @return void
     */
    public function clean(array $conditions)
    {
        if (!empty($conditions[\ECSPrefix20210517\Nette\Caching\Cache::ALL])) {
            $this->data = [];
        }
    }
}
