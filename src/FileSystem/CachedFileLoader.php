<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\PackageBuilder\FileSystem\FileGuard;

final class CachedFileLoader
{
    /**
     * @var FileGuard
     */
    private $fileGuard;

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(FileGuard $fileGuard, CacheInterface $cache)
    {
        $this->fileGuard = $fileGuard;
        $this->cache = $cache;
    }

    public function getFileContent(SplFileInfo $fileInfo): string
    {
        $this->fileGuard->ensureFileExists($fileInfo->getPathname(), __METHOD__);

        $cacheKey = 'file_content_' . md5_file($fileInfo->getRealPath());

        $cachedFileContent = $this->cache->get($cacheKey);
        if ($cachedFileContent) {
            return $cachedFileContent;
        }

        $currentFileContent = $fileInfo->getContents();

        $this->cache->set($cacheKey, $cachedFileContent);

        return $currentFileContent;
    }
}
