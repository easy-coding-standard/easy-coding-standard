<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Cache\Simple;

use Nette\Utils\Strings;
use Symfony\Component\Cache\Simple\FilesystemCache;

final class FilesystemCacheFactory
{
    /**
     * @var string
     */
    private $cacheDirectory;

    public function __construct(string $cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    public function create(): FilesystemCache
    {
        return new FilesystemCache(Strings::webalize(getcwd()), 0, $this->cacheDirectory);
    }
}
