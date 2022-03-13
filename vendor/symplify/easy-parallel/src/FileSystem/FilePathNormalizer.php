<?php

declare (strict_types=1);
namespace ECSPrefix20220313\Symplify\EasyParallel\FileSystem;

use ECSPrefix20220313\Symplify\SmartFileSystem\SmartFileInfo;
final class FilePathNormalizer
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[]
     */
    public function resolveFilePathsFromFileInfos(array $fileInfos) : array
    {
        $filePaths = [];
        foreach ($fileInfos as $fileInfo) {
            $filePaths[] = $fileInfo->getRelativeFilePathFromCwd();
        }
        return $filePaths;
    }
}
