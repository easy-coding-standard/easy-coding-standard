<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\SniffRunner\DI\SniffRunnerExtension;

/**
 * @todo
 * Move to @see SniffRunnerExtension
 */
final class SniffFactory
{
    /**
     * @param string[]|int[][]|string[][] $classes
     * @return Sniff[]
     */
    public function createFromClasses(array $classes): array
    {
        $configuredClasses = $classes;

        $sniffs = [];
        foreach ($configuredClasses as $class => $config) {
            $sniffs[] = $this->create($class, $config);
        }

        return $sniffs;
    }

    /**
     * @param string $class
     * @param string[] $config
     */
    private function create(string $class, array $config): Sniff
    {
        $sniff = new $class;
        $this->configureSniff($sniff, $config);

        return $sniff;
    }

    /**
     * @param Sniff $sniff
     * @param string[] $config
     */
    private function configureSniff(Sniff $sniff, array $config): void
    {
        foreach ($config as $property => $value) {
            $sniff->$property = $value;
        }
    }
}
