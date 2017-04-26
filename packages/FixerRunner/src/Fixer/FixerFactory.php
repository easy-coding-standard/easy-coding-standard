<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Fixer;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\FixerRunner\DI\FixerRunnerExtension;

/**
 * @todo
 * Move to @see FixerRunnerExtension
 */
final class FixerFactory
{
    /**
     * @param string[]
     * @return FixerInterface[]
     */
    public function createFromClasses(array $classes): array
    {
        $fixers = [];
        foreach ($classes as $class => $config) {
            $fixers[] = $this->create($class, $config);
        }

        return $fixers;
    }

    /**
     * @param string $class
     * @param mixed[] $config
     */
    private function create(string $class, array $config): FixerInterface
    {
        $fixer = new $class;
        $this->configureFixer($fixer, $config);

        return $fixer;
    }

    /**
     * @param FixerInterface $fixer
     * @param mixed[] $config
     */
    private function configureFixer(FixerInterface $fixer, array $config): void
    {
        if ($fixer instanceof ConfigurableFixerInterface && count($config)) {
            $fixer->configure($config);
        }
    }
}
