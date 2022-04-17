<?php

declare (strict_types=1);
namespace ECSPrefix20220417\Symplify\SmartFileSystem\Finder;

use ECSPrefix20220417\Nette\Utils\Finder as NetteFinder;
use SplFileInfo;
use ECSPrefix20220417\Symfony\Component\Finder\Finder as SymfonyFinder;
use ECSPrefix20220417\Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use ECSPrefix20220417\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\SmartFileSystem\Tests\Finder\FinderSanitizer\FinderSanitizerTest
 */
final class FinderSanitizer
{
    /**
     * @param mixed[]|NetteFinder|SymfonyFinder $files
     * @return SmartFileInfo[]
     */
    public function sanitize($files) : array
    {
        $smartFileInfos = [];
        foreach ($files as $file) {
            $fileInfo = \is_string($file) ? new \SplFileInfo($file) : $file;
            if (!$this->isFileInfoValid($fileInfo)) {
                continue;
            }
            /** @var string $realPath */
            $realPath = $fileInfo->getRealPath();
            $smartFileInfos[] = new \ECSPrefix20220417\Symplify\SmartFileSystem\SmartFileInfo($realPath);
        }
        return $smartFileInfos;
    }
    private function isFileInfoValid(\SplFileInfo $fileInfo) : bool
    {
        return (bool) $fileInfo->getRealPath();
    }
}
