<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching;

use Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage;

final readonly class Cache
{
    public function __construct(
        private FileCacheStorage $fileCacheStorage
    ) {
    }

    /**
     * @api
     */
    public function load(string $key, string $variableKey): ?string
    {
        return $this->fileCacheStorage->load($key, $variableKey);
    }

    /**
     * @api
     */
    public function save(string $key, string $variableKey, string $data): void
    {
        $this->fileCacheStorage->save($key, $variableKey, $data);
    }

    public function clear(): void
    {
        $this->fileCacheStorage->clear();
    }

    public function clean(string $cacheKey): void
    {
        $this->fileCacheStorage->clean($cacheKey);
    }
}
