<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix20220220\Symplify\Skipper\Skipper\Skipper;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
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
    public function __construct(\ECSPrefix20220220\Symplify\Skipper\Skipper\Skipper $skipper, \Symplify\EasyCodingStandard\Caching\ChangedFilesDetector $changedFilesDetector, \Symplify\EasyCodingStandard\Application\FileProcessorCollector $fileProcessorCollector)
    {
        $this->skipper = $skipper;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->fileProcessorCollector = $fileProcessorCollector;
    }
    /**
     * @return array<string, array<FileDiff|CodingStandardError>>
     */
    public function processFileInfo(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration) : array
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
