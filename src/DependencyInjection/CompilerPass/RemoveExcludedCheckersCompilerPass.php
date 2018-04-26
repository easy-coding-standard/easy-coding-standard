<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\EasyCodingStandard\Configuration\Option;

final class RemoveExcludedCheckersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $excludedCheckers = $this->getExcludedCheckersFromParameterBag($containerBuilder->getParameterBag());

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
    private function getExcludedCheckersFromParameterBag(ParameterBagInterface $parameterBag): array
    {
        if ($parameterBag->has(Option::EXCLUDE_CHECKERS)) {
            return (array) $parameterBag->get(Option::EXCLUDE_CHECKERS);
        }

        // typo proof
        if ($parameterBag->has('excluded_checkers')) {
            return (array) $parameterBag->get('excluded_checkers');
        }

        return [];
    }
}
