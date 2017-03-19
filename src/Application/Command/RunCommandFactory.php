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

    public function create(array $source, bool $isFixer, bool $shouldClearCache, array $configuration): RunCommand
    {
        $configuration = $this->normalizerCheckers($configuration);

        return new RunCommand($source, $isFixer, $shouldClearCache, $configuration);
    }

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
