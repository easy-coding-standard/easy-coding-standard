<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

final class CheckerConfigurationNormalizer
{
    /**
     * @param string[]|int[][]|string[][] $classes
     * @return string[][]
     */
    public static function normalize(array $classes): array
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
