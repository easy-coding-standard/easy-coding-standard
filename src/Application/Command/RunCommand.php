<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application\Command;

use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;

final class RunCommand
{
    /**
     * @var string
     */
    private const PHP_CODE_SNIFFER_KEY = 'php-code-sniffer';

    /**
     * @var string
     */
    private const PHP_CS_FIXER_KEY = 'php-cs-fixer';

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
        return $this->configuration[self::PHP_CODE_SNIFFER_KEY] ?? [];
    }

    /**
     * @return string[]|array[]
     */
    public function getFixers(): array
    {
        return $this->configuration[self::PHP_CS_FIXER_KEY] ?? [];
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
}
