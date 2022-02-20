<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use ECSPrefix20220220\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\EasyCodingStandard\ValueObject\Option;
final class RemoveExcludedCheckersCompilerPass implements \ECSPrefix20220220\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    public function process(\ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : void
    {
        $excludedCheckers = $this->getExcludedCheckersFromParameterBag($containerBuilder->getParameterBag());
        $definitions = $containerBuilder->getDefinitions();
        foreach ($definitions as $id => $definition) {
            if (!\in_array($definition->getClass(), $excludedCheckers, \true)) {
                continue;
            }
            $containerBuilder->removeDefinition($id);
        }
    }
    /**
     * @return array<int, class-string>
     */
    private function getExcludedCheckersFromParameterBag(\ECSPrefix20220220\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag) : array
    {
        // parts of "skip" parameter
        if (!$parameterBag->has(\Symplify\EasyCodingStandard\ValueObject\Option::SKIP)) {
            return [];
        }
        $excludedCheckers = [];
        $skip = (array) $parameterBag->get(\Symplify\EasyCodingStandard\ValueObject\Option::SKIP);
        foreach ($skip as $key => $value) {
            $excludedChecker = $this->matchFullClassSkip($key, $value);
            if ($excludedChecker === null) {
                continue;
            }
            $excludedCheckers[] = $excludedChecker;
        }
        return \array_unique($excludedCheckers);
    }
    /**
     * @param mixed $value
     * @return class-string|null
     * @param int|string $key
     */
    private function matchFullClassSkip($key, $value) : ?string
    {
        // "SomeClass::class" => null
        if (\is_string($key) && \class_exists($key) && $value === null) {
            return $key;
        }
        // "SomeClass::class"
        if (!\is_int($key)) {
            return null;
        }
        if (!\is_string($value)) {
            return null;
        }
        if (!\class_exists($value)) {
            return null;
        }
        return $value;
    }
}
