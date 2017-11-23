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
        ErrorCollector $errorCollector,
        Configuration $configuration
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->skipper = $skipper;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->errorCollector = $errorCollector;
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
        $changedFiles = $this->filterOnlyChangedFiles($files);

        // no files found
        if (! count($changedFiles)) {
            return;
        }

        if ($this->configuration->showProgressBar()) {
            $this->startProgressBar($changedFiles);
        }

        // 3. process found files by each processors
        $this->processFoundFiles($changedFiles);

        // 4. process files with DualRun
        $this->processFoundFilesSecondRun($changedFiles);
    }

    /**
     * @param SplFileInfo[] $files
     */
    private function startProgressBar(array $files): void
    {
        $this->easyCodingStandardStyle->startProgressBar(count($files) * 2);
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    private function processFoundFiles(array $fileInfos): void
    {
        foreach ($fileInfos as $relativePath => $fileInfo) {
            $this->easyCodingStandardStyle->advanceProgressBar();

            try {
                $this->sniffFileProcessor->processFile($fileInfo);
                $this->fixerFileProcessor->processFile($fileInfo);
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

    /**
     * @param SplFileInfo[] $fileInfos
     */
    private function processFoundFilesSecondRun(array $fileInfos): void
    {
        foreach ($fileInfos as $relativePath => $fileInfo) {
            $this->easyCodingStandardStyle->advanceProgressBar();

            $this->sniffFileProcessor->processFileSecondRun($fileInfo);
            $this->fixerFileProcessor->processFileSecondRun($fileInfo);
        }
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return SplFileInfo[]
     */
    private function filterOnlyChangedFiles(array $fileInfos): array
    {
        $changedFiles = [];

        foreach ($fileInfos as $relativePath => $fileInfo) {
            if ($this->changedFilesDetector->hasFileChanged($relativePath)) {
                $changedFiles[] = $fileInfo;

                $this->changedFilesDetector->addFile($relativePath);
            } else {
                $this->skipper->removeFileFromUnused($relativePath);
            }
        }

        return $changedFiles;
    }
}
