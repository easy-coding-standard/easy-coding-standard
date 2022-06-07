<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\Application;

use ECSPrefix20220607\Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use ECSPrefix20220607\Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Configuration;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix20220607\Symplify\Skipper\Skipper\Skipper;
use ECSPrefix20220607\Symplify\SmartFileSystem\SmartFileInfo;
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
    public function __construct(Skipper $skipper, ChangedFilesDetector $changedFilesDetector, FileProcessorCollector $fileProcessorCollector)
    {
        $this->skipper = $skipper;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->fileProcessorCollector = $fileProcessorCollector;
    }
    /**
     * @return array{file_diffs?: FileDiff[], coding_standard_errors?: CodingStandardError[]}
     */
    public function processFileInfo(SmartFileInfo $smartFileInfo, Configuration $configuration) : array
    {
        if ($this->skipper->shouldSkipFileInfo($smartFileInfo)) {
            return [];
        }
        $errorsAndDiffs = [];
        $this->changedFilesDetector->addFileInfo($smartFileInfo);
        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
        foreach ($fileProcessors as $fileProcessor) {
            if ($fileProcessor->getCheckers() === []) {
                continue;
            }
            $currentErrorsAndFileDiffs = $fileProcessor->processFile($smartFileInfo, $configuration);
            if ($currentErrorsAndFileDiffs === []) {
                continue;
            }
            $errorsAndDiffs = \array_merge($errorsAndDiffs, $currentErrorsAndFileDiffs);
        }
        // invalidate broken file, to analyse in next run too
        if ($errorsAndDiffs !== []) {
            $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
        }
        return $errorsAndDiffs;
    }
}
