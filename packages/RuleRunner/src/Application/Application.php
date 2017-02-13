<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Application;

use PhpCsFixer\Error\ErrorsManager;
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

    /**
     * @var ErrorsManager
     */
    private $errorsManager;

    public function __construct(RunnerFactory $runnerFactory, ErrorsManager $errorsManager)
    {
        $this->runnerFactory = $runnerFactory;
        $this->errorsManager = $errorsManager;
    }

    public function runCommand(RunApplicationCommand $command) : void
    {
        foreach ($command->getSources() as $source) {
            $this->runForSource($source, $command);
        }

        dump($this->errorsManager->isEmpty());
        // load errors to error Data collector
    }

    private function runForSource(string $source, RunApplicationCommand $command) : void
    {
        $runner = $this->createRunnerForSource($source, $command);
        $runner->fix();
    }

    private function createRunnerForSource(string $source, RunApplicationCommand $command) : Runner
    {
        return $this->runnerFactory->create(
            $command->getRules(),
            $command->getExcludedRules(),
            $source,
            $command->isFixer()
        );
    }
}
