<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class FileFilter
{
    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    /**
     * @var Skipper
     */
    private $skipper;

    public function __construct(ChangedFilesDetector $changedFilesDetector, Skipper $skipper)
    {
        $this->changedFilesDetector = $changedFilesDetector;
        $this->skipper = $skipper;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return SmartFileInfo[]
     */
    public function filterOnlyChangedFiles(array $fileInfos): array
    {
        $changedFiles = [];

        foreach ($fileInfos as $fileInfo) {
            if ($this->changedFilesDetector->hasFileInfoChanged($fileInfo)) {
                $changedFiles[] = $fileInfo;
            } else {
                $this->skipper->removeFileFromUnused($fileInfo->getRealPath());
            }
        }

        return $changedFiles;
    }
}
