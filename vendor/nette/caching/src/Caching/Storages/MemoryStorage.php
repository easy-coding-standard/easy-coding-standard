<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ECSPrefix20210601\Nette\Caching\Storages;

use ECSPrefix20210601\Nette;
/**
 * Memory cache storage.
 */
class MemoryStorage implements \ECSPrefix20210601\Nette\Caching\Storage
{
    use Nette\SmartObject;
    /** @var array */
    private $data = [];
    public function read(string $key)
    {
        return $this->data[$key] ?? null;
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
    public function write(string $key, $data, array $dependencies)
    {
        $this->data[$key] = $data;
    }
    /**
     * @return void
     */
    public function remove(string $key)
    {
        unset($this->data[$key]);
    }
    /**
     * @return void
     */
    public function clean(array $conditions)
    {
        if (!empty($conditions[\ECSPrefix20210601\Nette\Caching\Cache::ALL])) {
            $this->data = [];
        }
    }
}
