<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Cache;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;

final class CacheFactory
{
    public function create(): Cache
    {
        $tempDirectory = sys_get_temp_dir() . '/_changed_files_detector';
        FileSystem::createDir($tempDirectory);

        return new Cache(new FileStorage($tempDirectory));
    }
}
