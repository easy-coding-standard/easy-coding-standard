<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application\Command;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\ConfigurationOptions;
use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;

final class RunCommand
{
    /**
     * @var string[]
     */
    private $sources = [];

    /**
     * @var bool
     */
    private $isFixer;

    /**
     * @var string[]|array[]
     */
    private $configuration = [];

    /**
     * @var bool
     */
    private $shouldClearCache;

    /**
     * @param string[] $source
     * @param bool $isFixer
     * @param bool $shouldClearCache
     * @param mixed[] $configuration
     */
    public function __construct(array $source, bool $isFixer, bool $shouldClearCache, array $configuration)
    {
        $this->setSources($source);
        $this->isFixer = $isFixer;
        $this->shouldClearCache = $shouldClearCache;
        $this->configuration = $configuration;
    }

    /**
     * @return string[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @return mixed[]
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function isFixer(): bool
    {
        return $this->isFixer;
    }

    public function shouldClearCache(): bool
    {
        return $this->shouldClearCache;
    }

    /**
     * @return mixed[][]
     */
    public function getSniffs(): array
    {
        return $this->filterClassesByType($this->getCheckers(), Sniff::class);
    }

    /**
     * @return mixed[][]
     */
    public function getFixers(): array
    {
        return $this->filterClassesByType($this->getCheckers(), FixerInterface::class);
    }

    /**
     * @return string[][]
     */
    public function getSkipped(): array
    {
        return $this->configuration[ConfigurationOptions::SKIP] ?? [];
    }

    /**
     * @param string[] $sources
     */
    private function setSources(array $sources): void
    {
        $this->ensureSourcesExists($sources);
        $this->sources = $sources;
    }

    /**
     * @param string[] $sources
     */
    private function ensureSourcesExists(array $sources): void
    {
        foreach ($sources as $source) {
            if (file_exists($source)) {
                continue;
            }

            throw new SourceNotFoundException(sprintf(
                'Source "%s" does not exist.',
                $source
            ));
        }
    }

    /**
     * @param string[] $classes
     * @param string $type
     * @return mixed[]
     */
    private function filterClassesByType(array $classes, string $type): array
    {
        return array_filter($classes, function ($class) use ($type) {
            return is_a($class, $type, true);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @return string[]
     */
    private function getCheckers(): array
    {
        $checkers = $this->configuration[ConfigurationOptions::CHECKERS] ?? [];

        return ConfigurationNormalizer::normalizeClassesConfiguration($checkers);
    }
}
