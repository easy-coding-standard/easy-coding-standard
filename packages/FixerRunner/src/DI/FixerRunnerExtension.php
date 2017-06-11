<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\DI;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use Symplify\EasyCodingStandard\Configuration\Option\FixersOption;

final class FixerRunnerExtension // extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        $fixers = $this->getContainerBuilder()->parameters[FixersOption::NAME];
        $this->registerFixersAsServices($fixers);
    }

    /**
     * @param mixed[] $fixers
     */
    private function registerFixersAsServices(array $fixers): void
    {
        $containerBuilder = $this->getContainerBuilder();
        foreach ($fixers as $fixerClass => $configuration) {
            $fixerDefinition = $this->createFixerDefinition($fixerClass, $configuration);
            $containerBuilder->addDefinition(md5($fixerClass), $fixerDefinition);
        }
    }

    /**
     * @param mixed[] $configuration
     */
    private function createFixerDefinition(string $fixerClass, array $configuration): ServiceDefinition
    {
        $fixerDefinition = new ServiceDefinition;
        $fixerDefinition->setClass($fixerClass);

        if (count($configuration) && is_a($fixerClass, ConfigurableFixerInterface::class, true)) {
            $fixerDefinition->addSetup('configure', [$configuration]);
        }

        return $fixerDefinition;
    }
}
