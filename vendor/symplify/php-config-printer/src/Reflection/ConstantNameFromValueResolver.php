<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\Reflection;

use ReflectionClass;
final class ConstantNameFromValueResolver
{
    /**
     * @param mixed $constantValue
     * @return string|null
     */
    public function resolveFromValueAndClass($constantValue, string $class)
    {
        $reflectionClass = new \ReflectionClass($class);
        /** @var array<string, mixed> $constants */
        $constants = $reflectionClass->getConstants();
        foreach ($constants as $name => $value) {
            if ($value === $constantValue) {
                return $name;
            }
        }
        return null;
    }
}
