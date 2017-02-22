<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application\Command;

use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;

final class RunApplicationCommand
{
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
     * @param string[] $source
     * @param bool $isFixer
     * @param array[] $configuration
     */
    private function __construct(array $source, bool $isFixer, array $configuration)
    {
        $this->setSources($source);
        $this->isFixer = $isFixer;
        $this->configuration = $configuration;
    }

    /**
     * @param string[] $source
     * @param bool $isFixer
     * @param array[] $data
     */
    public static function createFromSourceFixerAndData(array $source, bool $isFixer, array $data): self
    {
        return new self($source, $isFixer, $data);
    }

    /**
     * @return string[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @return array[]
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function isFixer(): bool
    {
        return $this->isFixer;
    }

    /**
     * @return string[]|array[]
     */
    public function getSniffs(): array
    {
        return $this->configuration['php-code-sniffer'] ?? [];
    }

    /**
     * @return string[]|array[]
     */
    public function getFixers(): array
    {
        return $this->configuration['php-cs-fixer'] ?? [];
    }

    /**
     * @param string[] $sources
     */
    private function setSources(array $sources): void
    {
        $this->ensureSourceExists($sources);
        $this->sources = $sources;
    }

    /**
     * @param string[] $sources
     */
    private function ensureSourceExists(array $sources): void
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
