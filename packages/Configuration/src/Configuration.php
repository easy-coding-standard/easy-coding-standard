<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;
use function Safe\sprintf;

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
     * @var string
     */
    private $outputFormat = Option::TABLE_OUTPUT_FORMAT;

    /**
     * @var string[]
     */
    private $sources = [];

    public function resolveFromInput(InputInterface $input): void
    {
        /** @var string[] $sources */
        $sources = $input->getArgument(Option::SOURCE);
        $this->setSources($sources);
        $this->isFixer = (bool) $input->getOption(Option::FIX);
        $this->shouldClearCache = (bool) $input->getOption(Option::CLEAR_CACHE);
        $this->showProgressBar = $this->canShowProgressBar($input);
        $this->showErrorTable = ! (bool) $input->getOption(Option::NO_ERROR_TABLE);
        $this->outputFormat = $this->selectOutputFormat($input);
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

    public function showProgressBar(): bool
    {
        return $this->showProgressBar;
    }

    public function showErrorTable(): bool
    {
        return $this->showErrorTable;
    }

    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    /**
     * @param string[] $sources
     */
    private function setSources(array $sources): void
    {
        $this->ensureSourcesExists($sources);
        $this->sources = $this->normalizeSources($sources);
    }

    private function canShowProgressBar(InputInterface $input): bool
    {
        $notJsonOutput = $input->getOption(Option::OUTPUT_FORMAT_OPTION) !== Option::JSON_OUTPUT_FORMAT;
        $progressBarEnabled = ! (bool) $input->getOption(Option::NO_PROGRESS_BAR);

        return $notJsonOutput && $progressBarEnabled;
    }

    private function selectOutputFormat(InputInterface $input): string
    {
        $selectedFormat = $input->getOption(Option::OUTPUT_FORMAT_OPTION);
        $defaultFormat = $this->outputFormat;

        return in_array($selectedFormat, Option::OUTPUT_FORMATS, true)
            ? $selectedFormat
            : $defaultFormat;
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
