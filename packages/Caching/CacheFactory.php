<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching;

use Symfony\Component\Filesystem\Filesystem;
use Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage;
use Symplify\EasyCodingStandard\DependencyInjection\SimpleParameterProvider;
use Symplify\EasyCodingStandard\ValueObject\Option;

/**
 * @api
 */
final class CacheFactory
{
    public function __construct(
        private readonly Filesystem $fileSystem
    ) {
    }

    /**
     * @api
     */
    public function create(): Cache
    {
        $cacheDirectory = SimpleParameterProvider::getStringParameter(Option::CACHE_DIRECTORY);

        // ensure cache directory exists
        if (! $this->fileSystem->exists($cacheDirectory)) {
            $this->fileSystem->mkdir($cacheDirectory);
        }

        $fileCacheStorage = new FileCacheStorage($cacheDirectory, $this->fileSystem);

        return new Cache($fileCacheStorage);
    }
}
