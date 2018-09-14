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
     * @var string[]
     */
    private $sources = [];

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

    public function resolveFromInput(InputInterface $input): void
    {
        $this->setSources($input->getArgument(Option::SOURCE));
        $this->isFixer = (bool) $input->getOption(Option::FIX);
        $this->shouldClearCache = (bool) $input->getOption(Option::CLEAR_CACHE);
        $this->showProgressBar = ! (bool) $input->getOption(Option::NO_PROGRESS_BAR);
        $this->showErrorTable = ! (bool) $input->getOption(Option::NO_ERROR_TABLE);
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

    /**
     * @param string[] $sources
     */
    private function setSources(array $sources): void
    {
        $this->ensureSourcesExists($sources);
        $this->sources = $this->normalizeSources($sources);
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
