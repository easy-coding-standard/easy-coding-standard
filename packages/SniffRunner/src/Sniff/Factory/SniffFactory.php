<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
use Symplify\EasyCodingStandard\SniffRunner\Exception\Sniff\NotASniffClassException;

final class SniffFactory
{
    /**
     * @var ConfigurationNormalizer
     */
    private $configurationNormalizer;

    public function __construct(ConfigurationNormalizer $configurationNormalizer)
    {
        $this->configurationNormalizer = $configurationNormalizer;
    }

    /**
     * @param string[] $classes
     * @return Sniff[]
     */
    public function createFromClasses(array $classes): array
    {
        $configuredClasses = $this->configurationNormalizer->normalizeClassesConfiguration($classes);

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
        $this->ensureIsSniffClass($class);

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

    private function ensureIsSniffClass(string $class): void
    {
        if (! is_a($class, Sniff::class, true)) {
            throw new NotASniffClassException(
                sprintf(
                    'Sniff class has to implement "%s". "%s" given.',
                    Sniff::class,
                    $class
                )
            );
        }
    }
}
