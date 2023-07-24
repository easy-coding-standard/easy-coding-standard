<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Illuminate\Container\Container;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use ReflectionProperty;
use Symplify\EasyCodingStandard\DependencyInjection\SimpleParameterProvider;
use Symplify\EasyCodingStandard\ValueObject\Option;

final class RemoveExcludedCheckersCompilerPass
{
    public function process(Container $container): void
    {
        $excludedCheckers = $this->getExcludedCheckersFromSkipParameter();

        foreach ($container->getBindings() as $classType => $closure) {
            if (!in_array($classType, $excludedCheckers, true)) {
                continue;
            }

            // remove service from container completely
            CompilerPassHelper::removeCheckerFromContainer($container, $classType);
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
        if (!is_int($key)) {
            return null;
        }

        if (!is_string($value)) {
            return null;
        }

        if (!class_exists($value)) {
            return null;
        }

        return $value;
    }
}
