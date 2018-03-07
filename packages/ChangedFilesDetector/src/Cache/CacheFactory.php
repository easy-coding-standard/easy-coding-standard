<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Cache;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class CacheFactory
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }

    public function create(): Cache
    {
        $cacheDirectory = $this->getCacheDirectory();
        FileSystem::createDir($cacheDirectory);

        return new Cache(new FileStorage($cacheDirectory));
    }

    private function getCacheDirectory(): string
    {
        $cacheDirectory = $this->parameterProvider->provideParameter('cache_directory');
        if ($cacheDirectory === null) {
            $cacheDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . '_changed_files_detector';
        }

        if (defined('PHPUNIT_RUN')) { // defined in phpunit.xml
            // use different directory for tests, to avoid clearing local cache
            return rtrim($cacheDirectory, '/') . '_tests';
        }

        return $cacheDirectory;
    }
}
