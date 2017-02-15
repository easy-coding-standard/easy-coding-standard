<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Application;

use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;
use Symplify\EasyCodingStandard\RuleRunner\Runner\Runner;
use Symplify\EasyCodingStandard\RuleRunner\Runner\RunnerFactory;

final class Application implements ApplicationInterface
{
    /**
     * @var RunnerFactory
     */
    private $runnerFactory;

    public function __construct(RunnerFactory $runnerFactory)
    {
        $this->runnerFactory = $runnerFactory;
    }

    public function runCommand(RunApplicationCommand $command) : void
    {
        foreach ($command->getSources() as $source) {
            $this->runForSource($source, $command);
        }
    }

    private function runForSource(string $source, RunApplicationCommand $command) : void
    {
        $runner = $this->createRunnerForSource($source, $command);
        $runner->fix();
    }

    private function createRunnerForSource(string $source, RunApplicationCommand $command) : Runner
    {
        return $this->runnerFactory->create(
            $command->getFixers(),
            $source,
            $command->isFixer()
        );
    }
}
