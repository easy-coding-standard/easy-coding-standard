<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching;

use ECSPrefix202302\Symfony\Component\Filesystem\Filesystem;
use Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202302\Symplify\PackageBuilder\Parameter\ParameterProvider;
final class CacheFactory
{
    /**
     * @readonly
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    /**
     * @readonly
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fileSystem;
    public function __construct(ParameterProvider $parameterProvider, Filesystem $fileSystem)
    {
        $this->parameterProvider = $parameterProvider;
        $this->fileSystem = $fileSystem;
    }
    /**
     * @api
     */
    public function create() : \Symplify\EasyCodingStandard\Caching\Cache
    {
        $cacheDirectory = $this->parameterProvider->provideStringParameter(Option::CACHE_DIRECTORY);
        // ensure cache directory exists
        if (!$this->fileSystem->exists($cacheDirectory)) {
            $this->fileSystem->mkdir($cacheDirectory);
        }
        $fileCacheStorage = new FileCacheStorage($cacheDirectory, $this->fileSystem);
        return new \Symplify\EasyCodingStandard\Caching\Cache($fileCacheStorage);
    }
}
