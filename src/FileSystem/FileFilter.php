<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;

final class FileFilter
{
    public function __construct(
        private readonly ChangedFilesDetector $changedFilesDetector
    ) {
    }

    /**
     * @param string[] $filePaths
     * @return string[]
     */
    public function filterOnlyChangedFiles(array $filePaths): array
    {
        return array_filter(
            $filePaths,
            fn (string $filePath): bool => $this->changedFilesDetector->hasFileChanged($filePath)
        );
    }
}
