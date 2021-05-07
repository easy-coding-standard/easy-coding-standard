<?php

namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\SmartFileSystem\SmartFileInfo;
final class FileFilter
{
    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;
    /**
     * @param \Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector $changedFilesDetector
     */
    public function __construct($changedFilesDetector)
    {
        $this->changedFilesDetector = $changedFilesDetector;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return mixed[]
     */
    public function filterOnlyChangedFiles(array $fileInfos)
    {
        return \array_filter($fileInfos, function (SmartFileInfo $smartFileInfo) : bool {
            return $this->changedFilesDetector->hasFileInfoChanged($smartFileInfo);
        });
    }
}
