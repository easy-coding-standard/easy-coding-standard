<?php

declare (strict_types=1);
namespace ECSPrefix202306\Symplify\EasyParallel\FileSystem;

use ECSPrefix202306\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @api
 */
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
