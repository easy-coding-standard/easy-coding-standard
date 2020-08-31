<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Skipper;
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

    public function __construct(
        Skipper $skipper,
        ChangedFilesDetector $changedFilesDetector,
        ErrorAndDiffCollector $errorAndDiffCollector,
        FileProcessorCollector $fileProcessorCollector
    ) {
        $this->skipper = $skipper;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->fileProcessorCollector = $fileProcessorCollector;
    }

    public function processFileInfo(SmartFileInfo $smartFileInfo): void
    {
        try {
            $this->changedFilesDetector->addFileInfo($smartFileInfo);
            foreach ($this->fileProcessorCollector->getFileProcessors() as $fileProcessor) {
                if ($fileProcessor->getCheckers() === []) {
                    continue;
                }

                if ($this->skipper->shouldSkipFileInfo($smartFileInfo)) {
                    continue;
                }

                $fileProcessor->processFile($smartFileInfo);
            }
        } catch (ParseError $parseError) {
            $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
            $this->errorAndDiffCollector->addErrorMessage(
                $smartFileInfo,
                $parseError->getLine(),
                $parseError->getMessage(),
                ParseError::class
            );
        }
    }
}
