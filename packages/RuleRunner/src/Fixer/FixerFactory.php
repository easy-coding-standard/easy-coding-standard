<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Fixer;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;

final class FixerFactory
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
     * @return FixerInterface[]
     */
    public function createFromClasses(array $classes): array
    {
        $configuredClasses = $this->configurationNormalizer->normalizeClassesConfiguration($classes);

        $fixers = [];
        foreach ($configuredClasses as $class => $config) {
            $fixers[] = $this->create($class, $config);
        }

        return $fixers;
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
            $fixer->configure(count($config) ? $config : null);
        }
    }
}
