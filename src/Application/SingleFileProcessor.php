<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use ECSPrefix20210618\Symplify\Skipper\Skipper\Skipper;
use ECSPrefix20210618\Symplify\SmartFileSystem\SmartFileInfo;
final class SingleFileProcessor
{
    /**
     * @var \Symplify\Skipper\Skipper\Skipper
     */
    private $skipper;
    /**
     * @var \Symplify\EasyCodingStandard\Caching\ChangedFilesDetector
     */
    private $changedFilesDetector;
    /**
     * @var \Symplify\EasyCodingStandard\Application\FileProcessorCollector
     */
    private $fileProcessorCollector;
    public function __construct(\ECSPrefix20210618\Symplify\Skipper\Skipper\Skipper $skipper, \Symplify\EasyCodingStandard\Caching\ChangedFilesDetector $changedFilesDetector, \Symplify\EasyCodingStandard\Application\FileProcessorCollector $fileProcessorCollector)
    {
        $this->skipper = $skipper;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->fileProcessorCollector = $fileProcessorCollector;
    }
    /**
     * @return array<SystemError|FileDiff|CodingStandardError>
     */
    public function processFileInfo(\ECSPrefix20210618\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : array
    {
        if ($this->skipper->shouldSkipFileInfo($smartFileInfo)) {
            return [];
        }
        $errorsAndDiffs = [];
        try {
            $this->changedFilesDetector->addFileInfo($smartFileInfo);
            $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
            foreach ($fileProcessors as $fileProcessor) {
                if ($fileProcessor->getCheckers() === []) {
                    continue;
                }
                $currentErrorsAndFileDiffs = $fileProcessor->processFile($smartFileInfo);
                if ($currentErrorsAndFileDiffs === []) {
                    continue;
                }
                $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
                $errorsAndDiffs = \array_merge($errorsAndDiffs, $currentErrorsAndFileDiffs);
            }
        } catch (\ParseError $parseError) {
            $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
            $errorsAndDiffs[] = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($parseError->getLine(), $parseError->getMessage(), $smartFileInfo->getRelativeFilePathFromCwd());
        }
        return $errorsAndDiffs;
    }
}
