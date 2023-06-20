<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\DependencyInjection\SimpleParameterProvider;
use Symplify\EasyCodingStandard\ValueObject\Option;

final class RemoveExcludedCheckersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $excludedCheckers = $this->getExcludedCheckersFromSkipParameter();

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
    private function getExcludedCheckersFromSkipParameter(): array
    {
        $excludedCheckers = [];

        $skip = SimpleParameterProvider::getArrayParameter(Option::SKIP);
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
     * @return class-string|null
     */
    private function matchFullClassSkip(int|string $key, mixed $value): ?string
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
