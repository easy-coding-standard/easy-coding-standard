<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching;

use Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage;
final class Cache
{
    /**
     * @var \Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage
     */
    private $fileCacheStorage;
    public function __construct(\Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage $fileCacheStorage)
    {
        $this->fileCacheStorage = $fileCacheStorage;
    }
    /**
     * @return mixed|null
     */
    public function load(string $key, string $variableKey)
    {
        return $this->fileCacheStorage->load($key, $variableKey);
    }
    /**
     * @param mixed $data
     * @return void
     */
    public function save(string $key, string $variableKey, $data)
    {
        $this->fileCacheStorage->save($key, $variableKey, $data);
    }
    /**
     * @return void
     */
    public function clear()
    {
        $this->fileCacheStorage->clear();
    }
    /**
     * @return void
     */
    public function clean(string $cacheKey)
    {
        $this->fileCacheStorage->clean($cacheKey);
    }
}
