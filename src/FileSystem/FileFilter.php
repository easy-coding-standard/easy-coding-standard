<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Skipper;

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
     * @param SplFileInfo[] $fileInfos
     * @return SplFileInfo[]
     */
    public function filterOnlyChangedFiles(array $fileInfos): array
    {
        $changedFiles = [];

        foreach ($fileInfos as $relativePath => $fileInfo) {
            if ($this->changedFilesDetector->hasFileInfoChanged($fileInfo)) {
                $changedFiles[$relativePath] = $fileInfo;
            } else {
                $this->skipper->removeFileFromUnused($fileInfo->getRealPath());
            }
        }

        return $changedFiles;
    }
}
