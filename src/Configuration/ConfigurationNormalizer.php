<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

final class ConfigurationNormalizer
{
    /**
     * @param string[]|int[][]|string[][] $classes
     * @return string[][]
     */
    public function normalizeClassesConfiguration(array $classes): array
    {
        $configuredClasses = [];
        foreach ($classes as $name => $class) {
            if (is_array($class)) {
                $config = $class;
            } else {
                $name = $class;
                $config = [];
            }

            $configuredClasses[$name] = $config;
        }

        return $configuredClasses;
    }
}
