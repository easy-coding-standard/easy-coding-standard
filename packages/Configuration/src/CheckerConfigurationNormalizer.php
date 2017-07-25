<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use Symplify\EasyCodingStandard\Configuration\Exception\DuplicatedCheckerFoundException;
use Symplify\EasyCodingStandard\Configuration\Exception\InvalidConfigurationTypeException;

final class CheckerConfigurationNormalizer
{
    /**
     * @param string[]|int[][]|string[][] $classes
     * @return string[][]
     */
    public function normalize(array $classes): array
    {
        $configuredClasses = [];
        foreach ($classes as $name => $class) {
            if ($class === null) { // checker with commented configuration
                $config = [];
            } elseif (is_array($class)) { // checker with configuration
                $config = $class;
            } elseif (! is_string($name)) { // only checker item
                $name = $class;
                $config = [];
            } else {
                $config = $class;
                throw new InvalidConfigurationTypeException(sprintf(
                    'Configuration of "%s" checker has to be array; "%s" given with "%s".',
                    $name,
                    gettype($config),
                    $config
                ));
            }

            $this->ensureThereAreNoDuplications($configuredClasses, $name, $config);
            $configuredClasses[$name] = $config;
        }

        return $configuredClasses;
    }

    /**
     * @param string[] $configuredClasses
     * @param mixed[] $config
     */
    private function ensureThereAreNoDuplications(array $configuredClasses, string $name, array $config): void
    {
        if (! isset($configuredClasses[$name])) {
            return;
        }

        // new configuration? => not a duplicate, merge it
        if ($configuredClasses[$name] !== $config) {
            return;
        }

        throw new DuplicatedCheckerFoundException(sprintf(
            'Checker "%s" is being registered twice.'
             . ' Keep it only once, so configuration is clear and performance better.',
            $name
        ));
    }
}
