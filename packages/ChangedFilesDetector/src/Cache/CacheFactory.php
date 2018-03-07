<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Cache;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;

final class CacheFactory
{
    public function create(string $cacheDirectory): Cache
    {
        $this->getCacheDirectory($cacheDirectory);
        FileSystem::createDir($cacheDirectory);

        return new Cache(new FileStorage($cacheDirectory));
    }

    private function getCacheDirectory(string &$cacheDir): void
    {
        if (defined('PHPUNIT_RUN')) { // defined in phpunit.xml
            // use different directory for tests, to avoid clearing local cache
            $cacheDir .= '_tests';
        }
    }
}
