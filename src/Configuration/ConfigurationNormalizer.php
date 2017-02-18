<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

final class ConfigurationNormalizer
{
    public function normalizeClassesConfiguration(array $classes): array
    {
        $configuredClasses = [];
        foreach ($classes as $name => $class) {
            if (is_array($class)) {
                $config = $class;
                $configuredClasses[$name] = $config;
            } else {
                $name = $class;
                $configuredClasses[$name] = [];
            }
        }

        return $configuredClasses;
    }
}
