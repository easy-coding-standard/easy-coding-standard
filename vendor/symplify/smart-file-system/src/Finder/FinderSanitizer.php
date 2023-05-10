<?php

declare (strict_types=1);
namespace ECSPrefix202305\Symplify\SmartFileSystem\Finder;

use ECSPrefix202305\Symfony\Component\Finder\Finder;
use ECSPrefix202305\Symfony\Component\Finder\SplFileInfo;
use ECSPrefix202305\Symplify\SmartFileSystem\SmartFileInfo;
final class FinderSanitizer
{
    /**
     * @return SmartFileInfo[]
     */
    public function sanitize(Finder $finder) : array
    {
        $smartFileInfos = [];
        foreach ($finder as $fileInfo) {
            if (!$this->isFileInfoValid($fileInfo)) {
                continue;
            }
            /** @var string $realPath */
            $realPath = $fileInfo->getRealPath();
            $smartFileInfos[] = new SmartFileInfo($realPath);
        }
        return $smartFileInfos;
    }
    private function isFileInfoValid(SplFileInfo $fileInfo) : bool
    {
        return (bool) $fileInfo->getRealPath();
    }
}
