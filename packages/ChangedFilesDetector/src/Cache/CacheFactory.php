<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Cache;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;

final class CacheFactory
{
    /**
     * @var string
     */
    private $cacheDirectory;

    public function __construct(string $cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    public function create(): Cache
    {
        $cacheDirectory = $this->getCacheDirectory();
        FileSystem::createDir($cacheDirectory);

        return new Cache(new FileStorage($cacheDirectory));
    }

    private function getCacheDirectory(): string
    {
        if (defined('PHPUNIT_RUN')) { // defined in phpunit.xml
            // use different directory for tests, to avoid clearing local cache
            return rtrim($this->cacheDirectory, '/') . '_tests';
        }

        return $this->cacheDirectory;
    }
}
