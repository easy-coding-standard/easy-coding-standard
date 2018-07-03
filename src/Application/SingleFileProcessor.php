<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorCollectorInterface;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Skipper;

final class SingleFileProcessor implements FileProcessorCollectorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

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
     * @var FileProcessorInterface[]
     */
    private $fileProcessors = [];

    public function __construct(
        Configuration $configuration,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        Skipper $skipper,
        ChangedFilesDetector $changedFilesDetector,
        ErrorAndDiffCollector $errorAndDiffCollector
    ) {
        $this->configuration = $configuration;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->skipper = $skipper;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
    }

    public function addFileProcessor(FileProcessorInterface $fileProcessor): void
    {
        $this->fileProcessors[] = $fileProcessor;
    }

    public function processFileInfo(SplFileInfo $fileInfo): void
    {
        if ($this->configuration->showProgressBar()) {
            $this->easyCodingStandardStyle->progressAdvance();
        }

        try {
            $this->changedFilesDetector->addFileInfo($fileInfo);
            foreach ($this->fileProcessors as $fileProcessor) {
                if (! $fileProcessor->getCheckers()) {
                    continue;
                }

                if ($this->skipper->shouldSkipFile($fileInfo)) {
                    continue;
                }

                $fileProcessor->processFile($fileInfo);
            }
        } catch (ParseError $parseError) {
            $this->changedFilesDetector->invalidateFileInfo($fileInfo);
            $this->errorAndDiffCollector->addErrorMessage(
                $fileInfo,
                $parseError->getLine(),
                $parseError->getMessage(),
                ParseError::class
            );
        }
    }
}
