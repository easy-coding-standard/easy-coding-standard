<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application\Command;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;

final class RunCommand
{
    /**
     * @var string
     */
    public const OPTION_CHECKERS = 'checkers';

    /**
     * @var string[]
     */
    private $sources = [];

    /**
     * @var bool
     */
    private $isFixer = false;

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
    private function __construct(array $source, bool $isFixer, bool $shouldClearCache, array $configuration)
    {
        $this->setSources($source);
        $this->isFixer = $isFixer;
        $this->shouldClearCache = $shouldClearCache;
        $this->configuration = $configuration;
    }

    /**
     * @param string[] $source
     * @param bool $isFixer
     * @param bool $shouldClearCache
     * @param mixed[] $data
     */
    public static function createFromSourceFixerAndData(
        array $source,
        bool $isFixer,
        bool $shouldClearCache,
        array $data
    ): self {
        return new self($source, $isFixer, $shouldClearCache, $data);
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
     * @return string[]|array[]
     */
    public function getSniffs(): array
    {
        return isset($this->configuration[self::OPTION_CHECKERS])
            ? $this->filterClassesByType($this->configuration[self::OPTION_CHECKERS], Sniff::class)
            : [];
    }

    /**
     * @return string[]|array[]
     */
    public function getFixers(): array
    {
        return isset($this->configuration[self::OPTION_CHECKERS])
            ? $this->filterClassesByType($this->configuration[self::OPTION_CHECKERS], FixerInterface::class)
            : [];
    }

    /**
     * @return string[]
     */
    public function getSkipped(): array
    {
        return $this->configuration['skip'] ?? [];
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
     * @return string[]
     */
    private function filterClassesByType(array $classes, string $type): array
    {
        return array_filter($classes, function ($class) use ($type) {
            return is_a($class, $type, true);
        });
    }
}
