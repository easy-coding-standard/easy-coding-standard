<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use Nette\Utils\Finder as NetteFinder;
use SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class FinderSanitizer
{
    /**
     * @param NetteFinder|SymfonyFinder|SplFileInfo[]|SymfonySplFileInfo[]|string[] $files
     * @return SmartFileInfo[]
     */
    public function sanitize(iterable $files): array
    {
        $smartFileInfos = [];
        foreach ($files as $file) {
            $fileInfo = is_string($file) ? new SplFileInfo($file) : $file;
            if (! $this->isFileInfoValid($fileInfo)) {
                continue;
            }

            $smartFileInfos[] = new SmartFileInfo($fileInfo->getRealPath());
        }

        return $smartFileInfos;
    }

    private function isFileInfoValid(SplFileInfo $fileInfo): bool
    {
        if ($fileInfo->getRealPath() === false) {
            return false;
        }

        return (bool) filesize($fileInfo->getRealPath());
    }
}
