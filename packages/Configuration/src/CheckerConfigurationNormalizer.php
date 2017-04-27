<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use Symplify\EasyCodingStandard\Configuration\Exception\DuplicatedCheckerFoundException;

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

            self::ensureThereAreNoDuplications($configuredClasses, $name);
            $configuredClasses[$name] = $config;
        }

        return $configuredClasses;
    }

    /**
     * @param string[] $configuredClasses
     */
    private static function ensureThereAreNoDuplications(array $configuredClasses, string $name): void
    {
        if (! isset($configuredClasses[$name])) {
            return;
        }

        throw new DuplicatedCheckerFoundException(sprintf(
            'Checker "%s" is being registered twice.'
             . ' Keep it only once, so configuration is clear and performance better.',
            $name
        ));
    }
}
