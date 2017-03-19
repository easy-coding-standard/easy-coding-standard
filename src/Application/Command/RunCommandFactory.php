<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application\Command;

use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\ConfigurationOptions;

final class RunCommandFactory
{
    /**
     * @var ConfigurationNormalizer
     */
    private $configurationNormalizer;

    public function __construct(ConfigurationNormalizer $configurationNormalizer)
    {
        $this->configurationNormalizer = $configurationNormalizer;
    }

    /**
     * @param string[]|string[][] $source
     * @param bool $isFixer
     * @param bool $shouldClearCache
     * @param mixed[][] $configuration
     */
    public function create(array $source, bool $isFixer, bool $shouldClearCache, array $configuration): RunCommand
    {
        $configuration = $this->normalizerCheckers($configuration);

        return new RunCommand($source, $isFixer, $shouldClearCache, $configuration);
    }

    /**
     * @param mixed[][]|mixed[] $configuration
     * @return mixed[][]
     */
    private function normalizerCheckers(array $configuration): array
    {
        if (! isset($configuration[ConfigurationOptions::CHECKERS])) {
            $configuration[ConfigurationOptions::CHECKERS] = [];
        }

        $normalizedConfiguration = $this->configurationNormalizer->normalizeClassesConfiguration(
            $configuration[ConfigurationOptions::CHECKERS]
        );

        $configuration[ConfigurationOptions::CHECKERS] = $normalizedConfiguration;

        return $configuration;
    }
}
