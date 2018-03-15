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
        if ($this->isPHPUnit()) {
            // use different directory for tests, to avoid clearing local cache
            return rtrim($this->cacheDirectory, DIRECTORY_SEPARATOR) . '_tests';
        }

        return $this->cacheDirectory;
    }

    private function isPHPUnit(): bool
    {
        // defined by PHPUnit
        return defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__');
    }
}
