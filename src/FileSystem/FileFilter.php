<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use ECSPrefix202208\Symplify\SmartFileSystem\SmartFileInfo;
final class FileFilter
{
    /**
     * @var \Symplify\EasyCodingStandard\Caching\ChangedFilesDetector
     */
    private $changedFilesDetector;
    public function __construct(ChangedFilesDetector $changedFilesDetector)
    {
        $this->changedFilesDetector = $changedFilesDetector;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return SmartFileInfo[]
     */
    public function filterOnlyChangedFiles(array $fileInfos) : array
    {
        return \array_filter($fileInfos, function (SmartFileInfo $smartFileInfo) : bool {
            return $this->changedFilesDetector->hasFileInfoChanged($smartFileInfo);
        });
    }
}
