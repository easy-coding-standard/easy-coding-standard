<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Nette\Caching\Cache;
use SplFileInfo;
use Symplify\PackageBuilder\FileSystem\FileGuard;

final class CachedFileLoader
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var FileGuard
     */
    private $fileGuard;

    public function __construct(Cache $cache, FileGuard $fileGuard)
    {
        $this->cache = $cache;
        $this->fileGuard = $fileGuard;
    }

    public function getFileContent(SplFileInfo $fileInfo): string
    {
        $this->fileGuard->ensureFileExists($fileInfo->getPathname(), __METHOD__);

        $cacheKey = 'file_content_' . md5_file($fileInfo->getRealPath());

        $cachedFileContent = $this->cache->load($cacheKey);
        if ($cachedFileContent) {
            return $cachedFileContent;
        }

        $currentFileContent = $this->loadCurrentFileContent($fileInfo);

        $this->cache->save($cacheKey, $cachedFileContent);

        return $currentFileContent;
    }

    private function loadCurrentFileContent(SplFileInfo $fileInfo): string
    {
        return (string) file_get_contents($fileInfo->getRealPath());
    }
}
