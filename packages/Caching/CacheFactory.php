<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching;

use Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202206\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix202206\Symplify\SmartFileSystem\SmartFileSystem;
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
    public function __construct(ParameterProvider $parameterProvider, SmartFileSystem $smartFileSystem)
    {
        $this->parameterProvider = $parameterProvider;
        $this->smartFileSystem = $smartFileSystem;
    }
    public function create() : \Symplify\EasyCodingStandard\Caching\Cache
    {
        $cacheDirectory = $this->parameterProvider->provideStringParameter(Option::CACHE_DIRECTORY);
        // ensure cache directory exists
        if (!$this->smartFileSystem->exists($cacheDirectory)) {
            $this->smartFileSystem->mkdir($cacheDirectory);
        }
        $fileCacheStorage = new FileCacheStorage($cacheDirectory, $this->smartFileSystem);
        return new \Symplify\EasyCodingStandard\Caching\Cache($fileCacheStorage);
    }
}
