<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use SplFileInfo;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Contract\ChangedFilesDetectorInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;
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

        //
        foreach ($this->fileProcessors as $fileProcessor) {
            $fileProcessor->setupWithCommand($command);
        }

        // @todo: setup applications/fileRunners with command first
        // $this->application->configureWithCommand($command);

        // 4. process those files by each processors
        foreach ($files as $relativePath => $splFile) {
            // skip file if it didn't change
            if ($this->changedFilesDetector->hasFileChanged($relativePath) === false) {
                $this->easyCodingStandardStyle->advanceProgressBar();
                continue;
            }

            foreach ($this->fileProcessors as $fileProcessor) {
                $fileProcessor->processFile($splFile, $command->isFixer());
            }
            // dump($relativePath);

            // store changed file to cache

            // @OR: better just store if file had no errors?
            // processFile => return TRUE if ok, FALSE if failed
            // => drops redundant invalidate file method :)
            $this->changedFilesDetector->addFile($relativePath);
        }

        // @todo: find all files here and just process file?
        // might be faster, since it drops duplicate search and file creation
        // for cached file checks

//        foreach ($this->fileProcessors as $application) {
//            $application->runCommand($command);
//        }
    }

    /**
     * @param SplFileInfo[] $files
     */
    private function startProgressBar(array $files): void
    {
        // @todo: maybe add fixer count, might be more relevant?
        // or keep only file count?
        $max = count($files) * count($this->fileProcessors);
        $this->easyCodingStandardStyle->startProgressBar($max);
    }
}
