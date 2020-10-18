<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\EasyCodingStandard\ValueObject\Option;

final class RemoveExcludedCheckersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $excludedCheckers = $this->getExcludedCheckersFromParameterBag($containerBuilder->getParameterBag());

        $definitions = $containerBuilder->getDefinitions();
        foreach ($definitions as $id => $definition) {
            if (! in_array($definition->getClass(), $excludedCheckers, true)) {
                continue;
            }

            $containerBuilder->removeDefinition($id);
        }
    }

    /**
     * @return array<int, class-string>
     */
    private function getExcludedCheckersFromParameterBag(ParameterBagInterface $parameterBag): array
    {
        $excludedCheckers = [];

        // parts of "skip" parameter
        if ($parameterBag->has(Option::SKIP)) {
            $skip = $parameterBag->get(Option::SKIP);
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
