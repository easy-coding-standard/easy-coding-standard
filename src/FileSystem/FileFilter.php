<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FileSystem;

use SplFileInfo;
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
     * @param SplFileInfo[] $fileInfos
     * @return SplFileInfo[]
     */
    public function filterOnlyChangedFiles(array $fileInfos) : array
    {
        return \array_filter($fileInfos, function (SplFileInfo $fileInfo) : bool {
            return $this->changedFilesDetector->hasFileInfoChanged($fileInfo);
        });
    }
}
