<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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
        $excludedCheckers = [];

        // parts of "skip" parameter
        if ($parameterBag->has('skip')) {
            $skip = $parameterBag->get('skip');
            foreach ($skip as $key => $value) {
                if ($value !== null) {
                    continue;
                }

                if (! class_exists($key)) {
                    continue;
                }

                $excludedCheckers[] = $key;
            }
        }

        return array_unique($excludedCheckers);
    }
}
