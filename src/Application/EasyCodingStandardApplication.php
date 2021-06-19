<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FileSystem\FileFilter;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\Parallel\Application\ParallelFileProcessor;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use ECSPrefix20210619\Symplify\SmartFileSystem\SmartFileInfo;
final class EasyCodingStandardApplication
{
    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;
    /**
     * @var \Symplify\EasyCodingStandard\Finder\SourceFinder
     */
    private $sourceFinder;
    /**
     * @var \Symplify\EasyCodingStandard\Caching\ChangedFilesDetector
     */
    private $changedFilesDetector;
    /**
     * @var \Symplify\EasyCodingStandard\Configuration\Configuration
     */
    private $configuration;
    /**
     * @var \Symplify\EasyCodingStandard\FileSystem\FileFilter
     */
    private $fileFilter;
    /**
     * @var \Symplify\EasyCodingStandard\Application\SingleFileProcessor
     */
    private $singleFileProcessor;
    /**
     * @var \Symplify\EasyCodingStandard\Parallel\Application\ParallelFileProcessor
     */
    private $parallelFileProcessor;
    public function __construct(\Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle, \Symplify\EasyCodingStandard\Finder\SourceFinder $sourceFinder, \Symplify\EasyCodingStandard\Caching\ChangedFilesDetector $changedFilesDetector, \Symplify\EasyCodingStandard\Configuration\Configuration $configuration, \Symplify\EasyCodingStandard\FileSystem\FileFilter $fileFilter, \Symplify\EasyCodingStandard\Application\SingleFileProcessor $singleFileProcessor, \Symplify\EasyCodingStandard\Parallel\Application\ParallelFileProcessor $parallelFileProcessor)
    {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->configuration = $configuration;
        $this->fileFilter = $fileFilter;
        $this->singleFileProcessor = $singleFileProcessor;
        $this->parallelFileProcessor = $parallelFileProcessor;
    }
    /**
     * @return array<SystemError|FileDiff|CodingStandardError>
     */
    public function run() : array
    {
        // 1. find files in sources
        $fileInfos = $this->sourceFinder->find($this->configuration->getSources(), $this->configuration->doesMatchGitDiff());
        // 2. clear cache
        if ($this->configuration->shouldClearCache()) {
            $this->changedFilesDetector->clearCache();
        } else {
            $fileInfos = $this->fileFilter->filterOnlyChangedFiles($fileInfos);
        }
        // no files found
        $filesCount = \count($fileInfos);
        if ($filesCount === 0) {
            return [];
        }
        // process found files by each processors
        return $this->processFoundFiles($fileInfos);
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return array<SystemError|FileDiff|CodingStandardError>
     */
    private function processFoundFiles(array $fileInfos) : array
    {
        $fileInfoCount = \count($fileInfos);
        // 3. start progress bar
        $this->outputProgressBarAndDebugInfo($fileInfoCount);
        $errorsAndDiffs = [];
        foreach ($fileInfos as $fileInfo) {
            if ($this->easyCodingStandardStyle->isDebug()) {
                $this->easyCodingStandardStyle->writeln(' [file] ' . $fileInfo->getRelativeFilePathFromCwd());
            }
            try {
                $currentErrorsAndDiffs = $this->singleFileProcessor->processFileInfo($fileInfo);
                if ($currentErrorsAndDiffs !== []) {
                    $this->changedFilesDetector->invalidateFileInfo($fileInfo);
                }
                $errorsAndDiffs = \array_merge($errorsAndDiffs, $currentErrorsAndDiffs);
            } catch (\ParseError $parseError) {
                $this->changedFilesDetector->invalidateFileInfo($fileInfo);
                $errorsAndDiffs['system_errors'][] = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($parseError->getLine(), $parseError->getMessage(), $fileInfo->getRelativeFilePathFromCwd());
            }
            if ($this->easyCodingStandardStyle->isDebug()) {
                continue;
            }
            if ($this->configuration->shouldShowProgressBar()) {
                $this->easyCodingStandardStyle->progressAdvance();
            }
        }
        return $errorsAndDiffs;
    }
    /**
     * @return void
     */
    private function outputProgressBarAndDebugInfo(int $fileInfoCount)
    {
        if ($this->configuration->shouldShowProgressBar() && !$this->easyCodingStandardStyle->isDebug()) {
            $this->easyCodingStandardStyle->progressStart($fileInfoCount);
            // show more data on progress bar
            if ($this->easyCodingStandardStyle->isVerbose()) {
                $this->easyCodingStandardStyle->enableDebugProgressBar();
            }
        }
    }
}
