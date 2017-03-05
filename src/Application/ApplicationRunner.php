<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use SplFileInfo;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Contract\ChangedFilesDetectorInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\Skipper;

final class ApplicationRunner
{
    /**
     * @var ApplicationInterface[]
     */
    private $applications = [];

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

    public function addApplication(ApplicationInterface $application): void
    {
        $this->applications[] = $application;
    }

    public function runCommand(RunApplicationCommand $command): void
    {
        if ($command->shouldClearCache()) {
            $this->changedFilesDetector->clearCache();
        }

        $this->skipper->setSkipped($command->getSkipped());

        $files = $this->sourceFinder->find($command->getSources());
        $this->startProgressBar($files);

        // @todo: find all files here and just process file?
        // might be faster, since it drops duplicate search and file creation
        // for cached file checks

        foreach ($this->applications as $application) {
            $application->runCommand($command);
        }
    }

    /**
     * @param SplFileInfo[] $files
     */
    private function startProgressBar(array $files): void
    {
        $max = count($files) * count($this->applications);
        $this->easyCodingStandardStyle->progressBarStart($max);
    }
}
