<?php

namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\Skipper\Skipper\Skipper;
use Symplify\SmartFileSystem\SmartFileInfo;
final class SingleFileProcessor
{
    /**
     * @var Skipper
     */
    private $skipper;
    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;
    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;
    /**
     * @var FileProcessorCollector
     */
    private $fileProcessorCollector;
    /**
     * @param \Symplify\Skipper\Skipper\Skipper $skipper
     * @param \Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector $changedFilesDetector
     * @param \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector $errorAndDiffCollector
     * @param \Symplify\EasyCodingStandard\Application\FileProcessorCollector $fileProcessorCollector
     */
    public function __construct($skipper, $changedFilesDetector, $errorAndDiffCollector, $fileProcessorCollector)
    {
        $this->skipper = $skipper;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->fileProcessorCollector = $fileProcessorCollector;
    }
    /**
     * @return void
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     */
    public function processFileInfo($smartFileInfo)
    {
        if ($this->skipper->shouldSkipFileInfo($smartFileInfo)) {
            return;
        }
        try {
            $this->changedFilesDetector->addFileInfo($smartFileInfo);
            $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
            foreach ($fileProcessors as $fileProcessor) {
                if ($fileProcessor->getCheckers() === []) {
                    continue;
                }
                $fileProcessor->processFile($smartFileInfo);
            }
        } catch (ParseError $parseError) {
            $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
            $this->errorAndDiffCollector->addSystemErrorMessage($smartFileInfo, $parseError->getLine(), $parseError->getMessage());
        }
    }
}
