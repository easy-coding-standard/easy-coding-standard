<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use ECSPrefix20210610\Symplify\Skipper\Skipper\Skipper;
use ECSPrefix20210610\Symplify\SmartFileSystem\SmartFileInfo;
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
    public function __construct(\ECSPrefix20210610\Symplify\Skipper\Skipper\Skipper $skipper, \Symplify\EasyCodingStandard\Caching\ChangedFilesDetector $changedFilesDetector, \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector $errorAndDiffCollector, \Symplify\EasyCodingStandard\Application\FileProcessorCollector $fileProcessorCollector)
    {
        $this->skipper = $skipper;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->fileProcessorCollector = $fileProcessorCollector;
    }
    /**
     * @return void
     */
    public function processFileInfo(\ECSPrefix20210610\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
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
        } catch (\ParseError $parseError) {
            $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
            $this->errorAndDiffCollector->addSystemErrorMessage($smartFileInfo, $parseError->getLine(), $parseError->getMessage());
        }
    }
}
