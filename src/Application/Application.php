<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use SplFileInfo;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
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

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        SourceFinder $sourceFinder,
        ChangedFilesDetector $changedFilesDetector,
        Skipper $skipper,
        SniffFileProcessor $sniffFileProcessor,
        FixerFileProcessor $fixerFileProcessor
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->skipper = $skipper;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
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
        $this->fixerFileProcessor->setupWithCommand($command);
        $this->sniffFileProcessor->setupWithCommand($command);

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

            $this->fixerFileProcessor->processFile($fileInfo);
            $this->sniffFileProcessor->processFile($fileInfo);
        }
    }
}
