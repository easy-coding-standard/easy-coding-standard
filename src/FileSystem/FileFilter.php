<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
final class FileFilter
{
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Caching\ChangedFilesDetector
     */
    private $changedFilesDetector;
    public function __construct(ChangedFilesDetector $changedFilesDetector)
    {
        $this->changedFilesDetector = $changedFilesDetector;
    }
    /**
     * @param string[] $filePaths
     * @return string[]
     */
    public function filterOnlyChangedFiles(array $filePaths) : array
    {
        return \array_filter($filePaths, function (string $filePath) : bool {
            return $this->changedFilesDetector->hasFileChanged($filePath);
        });
    }
}
