<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FileSystem\FileFilter;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class EasyCodingStandardApplication
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
     * @var FileFilter
     */
    private $fileFilter;

    /**
     * @var SingleFileProcessor
     */
    private $singleFileProcessor;

    /**
     * @var FileProcessorCollector
     */
    private $fileProcessorCollector;

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        SourceFinder $sourceFinder,
        ChangedFilesDetector $changedFilesDetector,
        Configuration $configuration,
        FileFilter $fileFilter,
        SingleFileProcessor $singleFileProcessor,
        FileProcessorCollector $fileProcessorCollector
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->configuration = $configuration;
        $this->fileFilter = $fileFilter;
        $this->singleFileProcessor = $singleFileProcessor;
        $this->fileProcessorCollector = $fileProcessorCollector;
    }

    public function run(): int
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
        $filesCount = count($files);
        if ($filesCount === 0) {
            return 0;
        }

        // 3. start progress bar
        if ($this->configuration->shouldShowProgressBar() && ! $this->easyCodingStandardStyle->isVerbose()) {
            $this->easyCodingStandardStyle->progressStart($filesCount);
        }

        // 4. process found files by each processors
        $this->processFoundFiles($files);

        return $filesCount;
    }

    public function getCheckerCount(): int
    {
        $checkerCount = 0;

        foreach ($this->fileProcessorCollector->getFileProcessors() as $fileProcessor) {
            $checkerCount += count($fileProcessor->getCheckers());
        }

        return $checkerCount;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    private function processFoundFiles(array $fileInfos): void
    {
        foreach ($fileInfos as $fileInfo) {
            if ($this->easyCodingStandardStyle->isVerbose()) {
                $this->easyCodingStandardStyle->writeln(' [file] ' . $fileInfo->getRelativeFilePathFromCwd());
            }

            $this->singleFileProcessor->processFileInfo($fileInfo);

            if (! $this->easyCodingStandardStyle->isVerbose() && $this->configuration->shouldShowProgressBar()) {
                $this->easyCodingStandardStyle->progressAdvance();
            }
        }
    }
}
