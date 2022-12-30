<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FileFilter
{
    public function __construct(
        private ChangedFilesDetector $changedFilesDetector
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return SmartFileInfo[]
     */
    public function filterOnlyChangedFiles(array $fileInfos): array
    {
        return array_filter(
            $fileInfos,
            fn (SmartFileInfo $smartFileInfo): bool => $this->changedFilesDetector->hasFileInfoChanged($smartFileInfo)
        );
    }
}
