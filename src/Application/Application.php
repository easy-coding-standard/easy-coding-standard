<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use SplFileInfo;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

final class Application
{
    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        SourceFinder $sourceFinder,
        ChangedFilesDetector $changedFilesDetector,
        Skipper $skipper,
        SniffFileProcessor $sniffFileProcessor,
        FixerFileProcessor $fixerFileProcessor,
        ErrorCollector $errorReporter,
        Configuration $configuration
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->skipper = $skipper;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->errorCollector = $errorReporter;
        $this->configuration = $configuration;
    }

    public function run(): void
    {
        // 1. clear cache
        if ($this->configuration->shouldClearCache()) {
            $this->changedFilesDetector->clearCache();
        }

        // 2. find files in sources
        $files = $this->sourceFinder->find($this->configuration->getSources());

        // no files found
        if (! count($files)) {
            return;
        }

        $this->startProgressBar($files);

        // 3. process found files by each processors
        $this->processFoundFiles($files);
    }

    /**
     * @param SplFileInfo[] $files
     */
    private function startProgressBar(array $files): void
    {
        $this->easyCodingStandardStyle->startProgressBar(count($files));
    }

    /**
     * @param SplFileInfo[] $files
     */
    private function processFoundFiles(array $files): void
    {
        foreach ($files as $relativePath => $fileInfo) {
            $this->easyCodingStandardStyle->advanceProgressBar();

            // skip file if it didn't change
            if ($this->changedFilesDetector->hasFileChanged($relativePath) === false) {
                $this->skipper->removeFileFromUnused($relativePath);

                continue;
            }

            // add it elsewhere
            $this->changedFilesDetector->addFile($relativePath);

            try {
                $this->fixerFileProcessor->processFile($fileInfo);
                $this->sniffFileProcessor->processFile($fileInfo);
            } catch (ParseError $parseError) {
                $this->changedFilesDetector->invalidateFile($relativePath);
                $this->errorCollector->addErrorMessage(
                    $relativePath,
                    $parseError->getLine(),
                    $parseError->getMessage(),
                    ParseError::class,
                    false
                );
            }
        }
    }
}
