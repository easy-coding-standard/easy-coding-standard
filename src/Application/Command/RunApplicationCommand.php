<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;

final class RunApplicationCommand
{
    /**
     * @var array
     */
    private $sources = [];

    /**
     * @var bool
     */
    private $isFixer = false;

    /**
     * @var array
     */
    private $configuration = [];

    private function __construct(array $source, bool $isFixer, array $jsonConfiguration)
    {
        $this->setSources($source);
        $this->isFixer = $isFixer;
        $this->configuration = $jsonConfiguration;
    }

    public static function createFromInputAndData(InputInterface $input, array $data): self
    {
        return new self($input->getArgument('source'), $input->getOption('fix'), $data);
    }

    public static function createFromSourceFixerAndData(array $source, bool $isFixer, array $data): self
    {
        return new self($source, $isFixer, $data);
    }

    public function getSources(): array
    {
        return $this->sources;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function isFixer(): bool
    {
        return $this->isFixer;
    }

    public function getSniffs(): array
    {
        return $this->configuration['php-code-sniffer'] ?? [];
    }

    public function getFixers(): array
    {
        return $this->configuration['php-cs-fixer'] ?? [];
    }

    private function setSources(array $sources): void
    {
        $this->ensureSourceExists($sources);
        $this->sources = $sources;
    }

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
