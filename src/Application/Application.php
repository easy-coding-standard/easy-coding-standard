<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use SplFileInfo;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Contract\ChangedFilesDetectorInterface;
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
     * @var Skipper
     */
    private $skipper;

    /**
     * @var ChangedFilesDetectorInterface
     */
    private $changedFilesDetector;

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        SourceFinder $sourceFinder,
        Skipper $skipper,
        ChangedFilesDetectorInterface $changedFilesDetector
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->skipper = $skipper;
        $this->changedFilesDetector = $changedFilesDetector;
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

        // 2. set skipped checkers and their files
        $this->skipper->setSkipped($command->getSkipped());

        // 3. find files in sources
        $files = $this->sourceFinder->find($command->getSources());
        $this->startProgressBar($files);

        // 4. configure file processors
        foreach ($this->fileProcessors as $fileProcessor) {
            $fileProcessor->setupWithCommand($command);
        }

        // 5. process found files by each processors
        foreach ($files as $relativePath => $fileInfo) {
            // skip file if it didn't change
            if ($this->changedFilesDetector->hasFileChanged($relativePath) === false) {
                $this->easyCodingStandardStyle->advanceProgressBar();
                continue;
            }

            // add it elsewhere
            $this->changedFilesDetector->addFile($relativePath);

            foreach ($this->fileProcessors as $fileProcessor) {
                $fileProcessor->processFile($fileInfo);
            }
        }
    }

    /**
     * @param SplFileInfo[] $files
     */
    private function startProgressBar(array $files): void
    {
        $max = count($files);
        $this->easyCodingStandardStyle->startProgressBar($max);
    }
}
