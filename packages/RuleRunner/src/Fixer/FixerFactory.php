<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Fixer;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;

final class FixerFactory
{
    /**
     * @return FixerInterface[]
     */
    public function createFromClasses(array $classes) : array
    {
        $configuredClasses = $this->normalizeClassAndConfiguration($classes);

        $fixers = [];
        foreach ($configuredClasses as $class => $config) {
            $fixers[] = $this->create($class, $config);
        }

        return $fixers;
    }

    // todo: extract to common service!
    private function normalizeClassAndConfiguration(array $fixerClasses): array
    {
        $configuredFixers = [];
        foreach ($fixerClasses as $name => $class) {
            if (is_array($class)) {
                $config = $class;
                $configuredFixers[$name] = $config;
            } else {
                $name = $class;
                $configuredFixers[$name] = [];
            }
        }

        return $configuredFixers;
    }

    private function create(string $class, array $config): FixerInterface
    {
        $fixer = new $class;
        $this->configureFixer($fixer, $config);
        return $fixer;
    }

    private function configureFixer(FixerInterface $fixer, array $config): void
    {
        if ($fixer instanceof ConfigurableFixerInterface) {
            $fixer->configure($config);
        }
    }
}
