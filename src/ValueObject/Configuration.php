<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;

final class Configuration
{
    /**
     * @param string[] $sources
     * @param array<class-string<Sniff>> $reportSniffClassesWarnings
     */
    public function __construct(
        private readonly bool $isFixer = false,
        private readonly bool $shouldClearCache = false,
        private readonly bool $showProgressBar = true,
        private readonly bool $showErrorTable = true,
        private readonly array $sources = [],
        private readonly string $outputFormat = ConsoleOutputFormatter::NAME,
        private readonly bool $isParallel = false,
        private readonly ?string $config = null,
        private readonly string | null $parallelPort = null,
        private readonly string | null $parallelIdentifier = null,
        private readonly string | null $memoryLimit = null
    ) {
    }

    public function isFixer(): bool
    {
        return $this->isFixer;
    }

    public function shouldClearCache(): bool
    {
        return $this->shouldClearCache;
    }

    public function shouldShowProgressBar(): bool
    {
        return $this->showProgressBar;
    }

    public function shouldShowErrorTable(): bool
    {
        return $this->showErrorTable;
    }

    /**
     * @return string[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    public function isParallel(): bool
    {
        return $this->isParallel;
    }

    /**
     * @return array<class-string<Sniff>>
     */
    public function getReportSniffClassesWarnings(): array
    {
        return $this->reportSniffClassesWarnings;
    }

    public function getConfig(): ?string
    {
        return $this->config;
    }

    public function getParallelPort(): ?string
    {
        return $this->parallelPort;
    }

    public function getParallelIdentifier(): ?string
    {
        return $this->parallelIdentifier;
    }

    public function getMemoryLimit(): ?string
    {
        return $this->memoryLimit;
    }
}
