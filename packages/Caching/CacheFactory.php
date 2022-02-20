<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching;

use Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20220220\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem;
final class CacheFactory
{
    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(\ECSPrefix20220220\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem)
    {
        $this->parameterProvider = $parameterProvider;
        $this->smartFileSystem = $smartFileSystem;
    }
    public function create() : \Symplify\EasyCodingStandard\Caching\Cache
    {
        $cacheDirectory = $this->parameterProvider->provideStringParameter(\Symplify\EasyCodingStandard\ValueObject\Option::CACHE_DIRECTORY);
        // ensure cache directory exists
        if (!$this->smartFileSystem->exists($cacheDirectory)) {
            $this->smartFileSystem->mkdir($cacheDirectory);
        }
        $fileCacheStorage = new \Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage($cacheDirectory, $this->smartFileSystem);
        return new \Symplify\EasyCodingStandard\Caching\Cache($fileCacheStorage);
    }
}
