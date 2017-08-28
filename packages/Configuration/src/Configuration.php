<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;

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
    private $showPerformance = false;

    public function resolveFromInput(InputInterface $input): void
    {
        $this->setSources($input->getArgument('source'));
        $this->isFixer = (bool) $input->getOption('fix');
        $this->shouldClearCache = (bool) $input->getOption('clear-cache');
        $this->showPerformance = (bool) $input->getOption('show-performance');
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

    public function showPerformance(): bool
    {
        return $this->showPerformance;
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
