<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Configuration\Option\FixersOption;
use Symplify\EasyCodingStandard\FixerRunner\Contract\FixerCollectorInterface;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class FixerRunnerExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );

        $fixers = $this->getContainerBuilder()->parameters[FixersOption::NAME];
        $this->registerFixersAsServices($fixers);
    }

    public function beforeCompile(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            FixerCollectorInterface::class,
            FixerInterface::class,
            'addFixer'
        );
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
     * @param string $fixerClass
     * @param mixed[] $configuration
     */
    private function createFixerDefinition(string $fixerClass, array $configuration): ServiceDefinition
    {
        $fixerDefinition = new ServiceDefinition();
        $fixerDefinition->setClass($fixerClass);

        if (count($configuration) && is_a($fixerClass, ConfigurableFixerInterface::class, true)) {
            $fixerDefinition->addSetup('configure', [$configuration]);
        }

        return $fixerDefinition;
    }
}
