<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use Nette\Utils\Finder as NetteFinder;
use SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer as PackageBuilderFinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

/**
 * @deprecated Will be removed in Symplify 6.0
 * @see \Symplify\PackageBuilder\FileSystem\FinderSanitizer instead
 */
final class FinderSanitizer
{
    /**
     * @var PackageBuilderFinderSanitizer
     */
    private $packageBuilderFinderSanitizer;

    public function __construct(PackageBuilderFinderSanitizer $packageBuilderFinderSanitizer)
    {
        $this->packageBuilderFinderSanitizer = $packageBuilderFinderSanitizer;
    }

    /**
     * @param NetteFinder|SymfonyFinder|SplFileInfo[]|SymfonySplFileInfo[]|string[] $files
     * @return SmartFileInfo[]
     */
    public function sanitize(iterable $files): array
    {
        return $this->packageBuilderFinderSanitizer->sanitize($files);
    }
}
