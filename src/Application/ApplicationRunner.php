<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;
use Symplify\EasyCodingStandard\Finder\SourceFinder;

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

    public function __construct(EasyCodingStandardStyle $easyCodingStandardStyle, SourceFinder $sourceFinder)
    {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
    }

    public function addApplication(ApplicationInterface $application): void
    {
        $this->applications[] = $application;
    }

    public function runCommand(RunApplicationCommand $command): void
    {
        $files = $this->sourceFinder->find($command->getSources());
        $this->easyCodingStandardStyle->progressStart(count($files) * count($this->applications));

        foreach ($this->applications as $application) {
            $application->runCommand($command);
        }
    }
}
