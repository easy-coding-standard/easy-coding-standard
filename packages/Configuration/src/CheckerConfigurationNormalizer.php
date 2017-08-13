<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use Symplify\EasyCodingStandard\Configuration\Exception\InvalidConfigurationTypeException;

final class CheckerConfigurationNormalizer
{
    /**
     * @param mixed[] $classes
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

            $configuredClasses[$name] = $config;
        }

        return $configuredClasses;
    }
}
