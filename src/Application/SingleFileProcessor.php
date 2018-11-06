<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorCollectorInterface;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class SingleFileProcessor implements FileProcessorCollectorInterface
{
    /**
     * @var FileProcessorInterface[]
     */
    private $fileProcessors = [];

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

    public function __construct(
        Skipper $skipper,
        ChangedFilesDetector $changedFilesDetector,
        ErrorAndDiffCollector $errorAndDiffCollector
    ) {
        $this->skipper = $skipper;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
    }

    public function addFileProcessor(FileProcessorInterface $fileProcessor): void
    {
        $this->fileProcessors[] = $fileProcessor;
    }

    public function processFileInfo(SmartFileInfo $smartFileInfo): void
    {
        try {
            $this->changedFilesDetector->addFileInfo($smartFileInfo);
            foreach ($this->fileProcessors as $fileProcessor) {
                if (! $fileProcessor->getCheckers()) {
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
