<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FileSystem\FileFilter;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use ECSPrefix20210611\Symplify\SmartFileSystem\SmartFileInfo;
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
    public function __construct(\Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle, \Symplify\EasyCodingStandard\Finder\SourceFinder $sourceFinder, \Symplify\EasyCodingStandard\Caching\ChangedFilesDetector $changedFilesDetector, \Symplify\EasyCodingStandard\Configuration\Configuration $configuration, \Symplify\EasyCodingStandard\FileSystem\FileFilter $fileFilter, \Symplify\EasyCodingStandard\Application\SingleFileProcessor $singleFileProcessor)
    {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->configuration = $configuration;
        $this->fileFilter = $fileFilter;
        $this->singleFileProcessor = $singleFileProcessor;
    }
    public function run() : int
    {
        // 1. find files in sources
        $files = $this->sourceFinder->find($this->configuration->getSources(), $this->configuration->doesMatchGitDiff());
        // 2. clear cache
        if ($this->configuration->shouldClearCache()) {
            $this->changedFilesDetector->clearCache();
        } else {
            $files = $this->fileFilter->filterOnlyChangedFiles($files);
        }
        // no files found
        $filesCount = \count($files);
        if ($filesCount === 0) {
            return 0;
        }
        // 3. start progress bar
        if ($this->configuration->shouldShowProgressBar() && !$this->easyCodingStandardStyle->isDebug()) {
            $this->easyCodingStandardStyle->progressStart($filesCount);
            // show more data on progres bar
            if ($this->easyCodingStandardStyle->isVerbose()) {
                $this->easyCodingStandardStyle->enableDebugProgressBar();
            }
        }
        // 4. process found files by each processors
        $this->processFoundFiles($files);
        return $filesCount;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return void
     */
    private function processFoundFiles(array $fileInfos)
    {
        foreach ($fileInfos as $fileInfo) {
            if ($this->easyCodingStandardStyle->isDebug()) {
                $this->easyCodingStandardStyle->writeln(' [file] ' . $fileInfo->getRelativeFilePathFromCwd());
            }
            $this->singleFileProcessor->processFileInfo($fileInfo);
            if ($this->easyCodingStandardStyle->isDebug()) {
                continue;
            }
            if (!$this->configuration->shouldShowProgressBar()) {
                continue;
            }
            $this->easyCodingStandardStyle->progressAdvance();
        }
    }
}
