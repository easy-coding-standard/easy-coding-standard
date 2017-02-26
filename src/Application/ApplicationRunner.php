<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use SplFileInfo;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;
use Symplify\EasyCodingStandard\Error\ErrorFilter;
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
     * @var ErrorFilter
     */
    private $errorFilter;

    /**
     * @var Skipper
     */
    private $skipper;

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        SourceFinder $sourceFinder,
        ErrorFilter $errorFilter,
        Skipper $skipper
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->errorFilter = $errorFilter;
        $this->skipper = $skipper;
    }

    public function addApplication(ApplicationInterface $application): void
    {
        $this->applications[] = $application;
    }

    public function runCommand(RunApplicationCommand $command): void
    {
        $this->skipper->setIgnoredErrors($command->getIgnoredErrors());

        $files = $this->sourceFinder->find($command->getSources());
        $this->startProgressBar($files);

        foreach ($this->applications as $application) {
            $application->runCommand($command);
        }
    }

    /**
     * @param SplFileInfo[] $files
     */
    private function startProgressBar(array$files): void
    {
        $max = count($files) * count($this->applications);
        $this->easyCodingStandardStyle->progressBarStart($max);
    }
}
