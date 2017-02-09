<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;

final class ApplicationRunner
{
    /**
     * @var ApplicationInterface[]
     */
    private $applications = [];

    public function addApplication(ApplicationInterface $application)
    {
        $this->applications[] = $application;
    }

    public function runCommand(RunApplicationCommand $command)
    {
        foreach ($this->applications as $application) {
            $application->runCommand($command);
        }
    }
}
