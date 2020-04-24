<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use Jean85\PrettyVersions;
use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;

final class Configuration
{
    /**
     * @var bool
     */
    private $isFixer = false;

    /**
     * @var bool
     */
    private $shouldClearCache = false;

    /**
     * @var bool
     */
    private $showProgressBar = true;

    /**
     * @var bool
     */
    private $showErrorTable = true;

    /**
     * @var string|null
     */
    private $firstResolvedConfig;

    /**
     * @var string[]
     */
    private $sources = [];

    /**
     * @var string[]
     */
    private $paths = [];

    /**
     * @var string[]
     */
    private $sets = [];

    /**
     * @param string[] $paths
     * @param string[] $sets
     */
    public function __construct(array $paths, array $sets)
    {
        $this->paths = $paths;
        $this->sets = $sets;
    }

    /**
     * Needs to run in the start of the life cycle, since the rest of workflow uses it.
     */
    public function resolveFromInput(InputInterface $input): void
    {
        /** @var string[] $sources */
        $sources = $input->getArgument(Option::SOURCE);
        if ($sources !== []) {
            $this->setSources($sources);
        }

        $this->isFixer = (bool) $input->getOption(Option::FIX);
        $this->shouldClearCache = (bool) $input->getOption(Option::CLEAR_CACHE);
        $this->showProgressBar = $this->canShowProgressBar($input);
        $this->showErrorTable = ! (bool) $input->getOption(Option::NO_ERROR_TABLE);

        // CLI overrides config
        if ($input->getOption('set')) {
            $set = (string) $input->getOption('set');
            $this->sets = [$set];
        }
    }

    /**
     * @param mixed[] $options
     */
    public function resolveFromArray(array $options): void
    {
        $this->isFixer = (bool) $options['isFixer'];
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

    public function shouldShowProgressBar(): bool
    {
        return $this->showProgressBar;
    }

    public function shouldShowErrorTable(): bool
    {
        return $this->showErrorTable;
    }

    public function setFirstResolverConfig(?string $firstResolverConfig): void
    {
        $this->firstResolvedConfig = $firstResolverConfig;
    }

    public function getFirstResolverConfig(): ?string
    {
        return $this->firstResolvedConfig;
    }

    public function getPrettyVersion(): string
    {
        $version = PrettyVersions::getVersion('symplify/easy-coding-standard');

        return $version->getPrettyVersion();
    }

    /**
     * @param string[] $sources
     */
    public function setSources(array $sources): void
    {
        $this->ensureSourcesExists($sources);
        $this->sources = $this->normalizeSources($sources);
    }

    /**
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * @return string[]
     */
    public function getSets(): array
    {
        return $this->sets;
    }

    private function canShowProgressBar(InputInterface $input): bool
    {
        $notJsonOutput = $input->getOption(Option::OUTPUT_FORMAT_OPTION) !== JsonOutputFormatter::NAME;
        $progressBarEnabled = ! (bool) $input->getOption(Option::NO_PROGRESS_BAR);

        return $notJsonOutput && $progressBarEnabled;
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

            throw new SourceNotFoundException(sprintf('Source "%s" does not exist.', $source));
        }
    }

    /**
     * @param string[] $sources
     * @return string[]
     */
    private function normalizeSources(array $sources): array
    {
        foreach ($sources as $key => $value) {
            $sources[$key] = rtrim($value, DIRECTORY_SEPARATOR);
        }

        return $sources;
    }
}
