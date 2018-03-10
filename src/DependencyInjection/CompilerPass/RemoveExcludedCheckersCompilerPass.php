<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class RemoveExcludedCheckersCompilerPass implements CompilerPassInterface
{
    /**
     * @todo move to options
     * @var string
     */
    private const EXCLUDE_CHECKERS_OPTION = 'exclude_checkers';

    /**
     * @var string[]
     */
    private $excludedCheckers = [];

    public function process(ContainerBuilder $containerBuilder): void
    {
        $excludedCheckers = $this->resolveExcludedCheckersOption($containerBuilder->getParameterBag());

        foreach ($containerBuilder->getDefinitions() as $id => $definition) {
            if (! in_array($definition->getClass(), $excludedCheckers, true)) {
                continue;
            }

            $containerBuilder->removeDefinition($id);
        }
    }

    /**
     * @return string[]
     */
    private function resolveExcludedCheckersOption(ParameterBagInterface $parameterBag): array
    {
        if ($parameterBag->has(self::EXCLUDE_CHECKERS_OPTION)) {
            return $parameterBag->get(self::EXCLUDE_CHECKERS_OPTION);
        }

        // typo proof
        if ($parameterBag->has('excluded_checkers')) {
            return $parameterBag->get('excluded_checkers');
        }

        return [];
    }
}
