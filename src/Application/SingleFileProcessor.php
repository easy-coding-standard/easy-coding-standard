<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use ECSPrefix20210611\Symplify\Skipper\Skipper\Skipper;
use ECSPrefix20210611\Symplify\SmartFileSystem\SmartFileInfo;
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
     * @var \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;
    /**
     * @var \Symplify\EasyCodingStandard\Application\FileProcessorCollector
     */
    private $fileProcessorCollector;
    public function __construct(\ECSPrefix20210611\Symplify\Skipper\Skipper\Skipper $skipper, \Symplify\EasyCodingStandard\Caching\ChangedFilesDetector $changedFilesDetector, \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector $errorAndDiffCollector, \Symplify\EasyCodingStandard\Application\FileProcessorCollector $fileProcessorCollector)
    {
        $this->skipper = $skipper;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->fileProcessorCollector = $fileProcessorCollector;
    }
    /**
     * @return void
     */
    public function processFileInfo(\ECSPrefix20210611\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
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
