<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;

final class SniffFactory
{
    /**
     * @return Sniff[]
     */
    public function createFromClasses(array $classes): array
    {
        $configuredClasses = $this->normalizeClassAndConfiguration($classes);

        $sniffs = [];
        foreach ($configuredClasses as $class => $config) {
            $sniffs[] = $this->create($class, $config);
        }

        return $sniffs;
    }

    // todo: extract to common service!
    private function normalizeClassAndConfiguration(array $classes): array
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

    private function create(string $sniffClass, array $config): Sniff
    {
        $sniff = new $sniffClass;
        $this->configureSniff($sniff, $config);
        return $sniff;
    }

    private function configureSniff(Sniff $sniff, array $config): void
    {
        foreach ($config as $property => $value) {
            $sniff->$property = $value;
        }
    }
}
