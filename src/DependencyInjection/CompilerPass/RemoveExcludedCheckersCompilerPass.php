<?php

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\EasyCodingStandard\ValueObject\Option;

final class RemoveExcludedCheckersCompilerPass implements CompilerPassInterface
{
    /**
     * @return void
     */
    public function process(ContainerBuilder $containerBuilder)
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
     * @return mixed[]
     */
    private function getExcludedCheckersFromParameterBag(ParameterBagInterface $parameterBag)
    {

        // parts of "skip" parameter
        if (! $parameterBag->has(Option::SKIP)) {
            return [];
        }

        $excludedCheckers = [];

        $skip = (array) $parameterBag->get(Option::SKIP);
        foreach ($skip as $key => $value) {
            $excludedChecker = $this->matchFullClassSkip($key, $value);
            if ($excludedChecker === null) {
                continue;
            }

            $excludedCheckers[] = $excludedChecker;
        }

        return array_unique($excludedCheckers);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return string|null
     */
    private function matchFullClassSkip($key, $value)
    {
        // "SomeClass::class" => null
        if (is_string($key) && class_exists($key) && $value === null) {
            return $key;
        }
        // "SomeClass::class"
        if (! is_int($key)) {
            return null;
        }
        if (! is_string($value)) {
            return null;
        }
        if (! class_exists($value)) {
            return null;
        }
        return $value;
    }
}
