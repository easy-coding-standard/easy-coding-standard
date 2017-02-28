<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

final class ConfigurationNormalizer
{
    /**
     * @param string[]|string[][] $classes
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

    /**
     * @param string[]|string[][] $skipperRules
     * @return string[][]
     */
    public function normalizeSkipperConfiguration(array $skipperRules): array
    {
        $normalizedSkipperRules = [];
        foreach ($skipperRules as $file => $sourceClass) {
            if (class_exists($file)) {
                $files = [];
                if (is_array($sourceClass)) {
                    [$files, $sourceClass] = [$sourceClass, $file];
                } else {
                    [$file, $sourceClass] = [$sourceClass, $file];
                    $files[] = $file;
                }

                foreach ($files as $file) {
                    $normalizedSkipperRules[$file][] = $sourceClass;
                }
            } else {
                $normalizedSkipperRules[$file] = $sourceClass;
            }
        }

        return $normalizedSkipperRules;
    }
}
