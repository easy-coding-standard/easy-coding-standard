<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use SplFileInfo;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;

final class FileFilter
{
    public function __construct(
        private readonly ChangedFilesDetector $changedFilesDetector
    ) {
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return SplFileInfo[]
     */
    public function filterOnlyChangedFiles(array $fileInfos): array
    {
        return array_filter(
            $fileInfos,
            fn (SplFileInfo $fileInfo): bool => $this->changedFilesDetector->hasFileInfoChanged($fileInfo)
        );
    }
}
