<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Psr\SimpleCache\CacheInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

final class CachedFileLoader
{
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getFileContent(SmartFileInfo $smartFileInfo): string
    {
        // getRealPath() cannot be used in PHAR
        $cacheKey = 'file_content_' . md5_file($smartFileInfo->getRealPath() ?: $smartFileInfo->getPathname());

        $cachedFileContent = $this->cache->get($cacheKey);
        if ($cachedFileContent) {
            return $cachedFileContent;
        }

        $currentFileContent = $smartFileInfo->getContents();

        $this->cache->set($cacheKey, $cachedFileContent);

        return $currentFileContent;
    }
}
