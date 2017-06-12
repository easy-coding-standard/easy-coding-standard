<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use SplFileInfo;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\Skipper;

final class Application
{
    /**
     * @var FileProcessorInterface[]
     */
    private $fileProcessors = [];

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

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        SourceFinder $sourceFinder,
        ChangedFilesDetector $changedFilesDetector,
        Skipper $skipper
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->skipper = $skipper;
    }

    public function addFileProcessor(FileProcessorInterface $fileProcessor): void
    {
        $this->fileProcessors[] = $fileProcessor;
    }

    public function runCommand(RunCommand $command): void
    {
        // 1. clear cache
        if ($command->shouldClearCache()) {
            $this->changedFilesDetector->clearCache();
        }

        // 2. find files in sources
        $files = $this->sourceFinder->find($command->getSources());
        $this->startProgressBar($files);

        // 3. configure file processors
        foreach ($this->fileProcessors as $fileProcessor) {
            $fileProcessor->setupWithCommand($command);
        }

        // 4. process found files by each processors
        $this->processFoundFiles($files);
    }

    /**
     * @param SplFileInfo[] $files
     */
    private function startProgressBar(array $files): void
    {
        $max = count($files);
        $this->easyCodingStandardStyle->startProgressBar($max);
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

            foreach ($this->fileProcessors as $fileProcessor) {
                $fileProcessor->processFile($fileInfo);
            }
        }
    }
}
