<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application\Command;

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
     * @var bool
     */
    private $shouldClearCache;

    /**
     * @param string[] $source
     * @param bool $isFixer
     * @param bool $shouldClearCache
     */
    private function __construct(array $source, bool $isFixer, bool $shouldClearCache)
    {
        $this->setSources($source);
        $this->isFixer = $isFixer;
        $this->shouldClearCache = $shouldClearCache;
    }

    /**
     * @param string[] $source
     * @param bool $isFixer
     * @param bool $shouldClearCache
     */
    public static function createForSourceFixerAndClearCache(array $source, bool $isFixer, bool $shouldClearCache): self
    {
        return new self($source, $isFixer, $shouldClearCache);
    }

    /**
     * @return string[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    public function isFixer(): bool
    {
        return $this->isFixer;
    }

    public function shouldClearCache(): bool
    {
        return $this->shouldClearCache;
    }

//    /**
//     * @return mixed[][]
//     */
//    public function getSniffs(): array
//    {
//        return $this->filterClassesByType($this->getCheckers(), Sniff::class);
//    }
//
//    /**
//     * @return mixed[][]
//     */
//    public function getFixers(): array
//    {
//        return $this->filterClassesByType($this->getCheckers(), FixerInterface::class);
//    }

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

//    /**
//     * @param string[] $classes
//     * @param string $type
//     * @return mixed[]
//     */
//    private function filterClassesByType(array $classes, string $type): array
//    {
//        return array_filter($classes, function ($class) use ($type) {
//            return is_a($class, $type, true);
//        }, ARRAY_FILTER_USE_KEY);
//    }
//
//    /**
//     * @return string[][]
//     */
//    private function getCheckers(): array
//    {
//        $checkers = $this->configuration[ConfigurationOptions::CHECKERS] ?? [];
//
//        return ConfigurationNormalizer::normalizeClassesConfiguration($checkers);
//    }
}
