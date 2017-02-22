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
     * @param string $sniffClass
     * @param string[] $config
     */
    private function create(string $sniffClass, array $config): Sniff
    {
        $sniff = new $sniffClass;
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
