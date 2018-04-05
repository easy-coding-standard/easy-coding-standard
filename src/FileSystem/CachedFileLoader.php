<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Psr\SimpleCache\CacheInterface;
use SplFileInfo;
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
    private $symfonyCache;

    public function __construct(FileGuard $fileGuard, CacheInterface $symfonyCache)
    {
        $this->fileGuard = $fileGuard;
        $this->symfonyCache = $symfonyCache;
    }

    public function getFileContent(SplFileInfo $fileInfo): string
    {
        $this->fileGuard->ensureFileExists($fileInfo->getPathname(), __METHOD__);

        $cacheKey = 'file_content_' . md5_file($fileInfo->getRealPath());

        $cachedFileContent = $this->symfonyCache->get($cacheKey);
        if ($cachedFileContent) {
            return $cachedFileContent;
        }

        $currentFileContent = $this->loadCurrentFileContent($fileInfo);

        $this->symfonyCache->set($cacheKey, $cachedFileContent);

        return $currentFileContent;
    }

    private function loadCurrentFileContent(SplFileInfo $fileInfo): string
    {
        return (string) file_get_contents($fileInfo->getRealPath());
    }
}
