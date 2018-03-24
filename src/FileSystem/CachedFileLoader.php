<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Nette\Caching\Cache;
use SplFileInfo;

final class CachedFileLoader
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function getFileContent(SplFileInfo $fileInfo): string
    {
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
