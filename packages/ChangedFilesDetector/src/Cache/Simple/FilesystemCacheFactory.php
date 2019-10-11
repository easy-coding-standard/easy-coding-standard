<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Cache\Simple;

use Symfony\Component\Cache\Simple\FilesystemCache;

final class FilesystemCacheFactory
{
    /**
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var string
     */
    private $cacheNamespace;

    public function __construct(string $cacheDirectory, string $cacheNamespace)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->cacheNamespace = $cacheNamespace;
    }

    public function create(): FilesystemCache
    {
        return new FilesystemCache($this->cacheNamespace, 0, $this->cacheDirectory);
    }
}
