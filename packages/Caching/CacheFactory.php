<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching;

use Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\SmartFileSystem;

final class CacheFactory
{
    public function __construct(
        private ParameterProvider $parameterProvider,
        private SmartFileSystem $smartFileSystem
    ) {
    }

    /**
     * @api
     */
    public function create(): Cache
    {
        $cacheDirectory = $this->parameterProvider->provideStringParameter(Option::CACHE_DIRECTORY);

        // ensure cache directory exists
        if (! $this->smartFileSystem->exists($cacheDirectory)) {
            $this->smartFileSystem->mkdir($cacheDirectory);
        }

        $fileCacheStorage = new FileCacheStorage($cacheDirectory, $this->smartFileSystem);

        return new Cache($fileCacheStorage);
    }
}
