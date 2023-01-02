<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching;

use ECSPrefix202301\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix202301\Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage;
use Symplify\EasyCodingStandard\ValueObject\Option;

final class CacheFactory
{
    /**
     * @var \ECSPrefix202301\Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var \ECSPrefix202301\Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(ParameterProvider $parameterProvider, SmartFileSystem $smartFileSystem)
    {
        $this->parameterProvider = $parameterProvider;
        $this->smartFileSystem = $smartFileSystem;
    }

    /**
     * @api
     */
    public function create(): \Symplify\EasyCodingStandard\Caching\Cache
    {
        $cacheDirectory = $this->parameterProvider->provideStringParameter(Option::CACHE_DIRECTORY);
        // ensure cache directory exists
        if (! $this->smartFileSystem->exists($cacheDirectory)) {
            $this->smartFileSystem->mkdir($cacheDirectory);
        }
        $fileCacheStorage = new FileCacheStorage($cacheDirectory, $this->smartFileSystem);
        return new \Symplify\EasyCodingStandard\Caching\Cache($fileCacheStorage);
    }
}
