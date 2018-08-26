<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\DualRunAwareFileProcessorInterface;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorCollectorInterface;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\FileSystem\FileFilter;
use Symplify\EasyCodingStandard\Finder\SourceFinder;

final class Application implements FileProcessorCollectorInterface
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
     * @var Configuration
     */
    private $configuration;

    /**
     * @var FileProcessorInterface[]
     */
    private $fileProcessors = [];

    /**
     * @var FileFilter
     */
    private $fileFilter;

    /**
     * @var SingleFileProcessor
     */
    private $singleFileProcessor;

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        SourceFinder $sourceFinder,
        ChangedFilesDetector $changedFilesDetector,
        Configuration $configuration,
        FileFilter $fileFilter,
        SingleFileProcessor $singleFileProcessor
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->configuration = $configuration;
        $this->fileFilter = $fileFilter;
        $this->singleFileProcessor = $singleFileProcessor;
    }

    public function addFileProcessor(FileProcessorInterface $fileProcessor): void
    {
        $this->fileProcessors[] = $fileProcessor;
    }

    public function run(): void
    {
        // 1. find files in sources
        $files = $this->sourceFinder->find($this->configuration->getSources());

        // 2. clear cache
        if ($this->configuration->shouldClearCache()) {
            $this->changedFilesDetector->clearCache();
        } else {
            $files = $this->fileFilter->filterOnlyChangedFiles($files);
        }

        // no files found
        if (! count($files)) {
            return;
        }

        // 3. start progress bar
        if ($this->configuration->showProgressBar()) {
            $this->easyCodingStandardStyle->progressStart(count($files) * ($this->isDualRunEnabled() ? 2 : 1));
        }

        // 4. process found files by each processors
        $this->processFoundFiles($files);

        // 5. process files with DualRun
        $this->processFoundFilesSecondRun($files);
    }

    public function getCheckerCount(): int
    {
        $checkerCount = 0;

        foreach ($this->fileProcessors as $fileProcessor) {
            $checkerCount += count($fileProcessor->getCheckers());
        }

        return $checkerCount;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    private function processFoundFiles(array $fileInfos): void
    {
        foreach ($fileInfos as $fileInfo) {
            $this->singleFileProcessor->processFileInfo($fileInfo);
        }
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    private function processFoundFilesSecondRun(array $fileInfos): void
    {
        if (! $this->isDualRunEnabled()) {
            return;
        }

        foreach ($fileInfos as $fileInfo) {
            if ($this->configuration->showProgressBar()) {
                $this->easyCodingStandardStyle->progressAdvance();
            }

            foreach ($this->fileProcessors as $fileProcessor) {
                if ($fileProcessor instanceof DualRunAwareFileProcessorInterface) {
                    $fileProcessor->processFileSecondRun($fileInfo);
                }
            }
        }
    }

    private function isDualRunEnabled(): bool
    {
        foreach ($this->fileProcessors as $fileProcessor) {
            if (! $fileProcessor instanceof DualRunAwareFileProcessorInterface) {
                continue;
            }

            if ($fileProcessor->getDualRunCheckers()) {
                return true;
            }
        }

        return false;
    }
}
