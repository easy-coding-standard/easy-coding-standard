<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;

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
