<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Fixer;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
use Symplify\EasyCodingStandard\FixerRunner\Exception\Fixer\NotAFixerClassException;

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
     * @param string[]
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

    /**
     * @param string $class
     * @param mixed[] $config
     */
    private function create(string $class, array $config): FixerInterface
    {
        $this->ensureIsFixerClass($class);

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

    private function ensureIsFixerClass(string $class): void
    {
        if (! is_a($class, FixerInterface::class, true)) {
            throw new NotAFixerClassException(
                sprintf(
                    'Fixer class has to implement "%s". "%s" given.',
                    FixerInterface::class,
                    $class
                )
            );
        }
    }
}
