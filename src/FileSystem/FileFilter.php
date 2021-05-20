<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use ECSPrefix20210520\Symplify\SmartFileSystem\SmartFileInfo;
final class FileFilter
{
    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;
    public function __construct(\Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector $changedFilesDetector)
    {
        $this->changedFilesDetector = $changedFilesDetector;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return SmartFileInfo[]
     */
    public function filterOnlyChangedFiles(array $fileInfos) : array
    {
        return \array_filter($fileInfos, function (\ECSPrefix20210520\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : bool {
            return $this->changedFilesDetector->hasFileInfoChanged($smartFileInfo);
        });
    }
}
