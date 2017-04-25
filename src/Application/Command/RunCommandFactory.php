<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application\Command;

use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\ConfigurationOptions;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;

final class RunCommandFactory
{
    /**
     * @var ConfigurationNormalizer
     */
    private $configurationNormalizer;

    /**
     * @var CheckerTypeValidator
     */
    private $checkerTypeValidator;

    public function __construct(
        ConfigurationNormalizer $configurationNormalizer,
        CheckerTypeValidator $checkerTypeValidator
    ) {
        $this->configurationNormalizer = $configurationNormalizer;
        $this->checkerTypeValidator = $checkerTypeValidator;
    }

    /**
     * @param string[]|string[][] $source
     * @param bool $isFixer
     * @param bool $shouldClearCache
     * @param mixed[][] $configuration
     */
    public function create(array $source, bool $isFixer, bool $shouldClearCache, array $configuration): RunCommand
    {
        $configuration = $this->prepareDefaults($configuration);
        $configuration = $this->normalizerCheckers($configuration);

        $this->validateCheckerType($configuration);

        return new RunCommand($source, $isFixer, $shouldClearCache, $configuration);
    }

    /**
     * @param mixed[][]|mixed[] $configuration
     * @return mixed[][]
     */
    private function normalizerCheckers(array $configuration): array
    {
        $normalizedConfiguration = $this->configurationNormalizer->normalizeClassesConfiguration(
            $configuration[ConfigurationOptions::CHECKERS]
        );

        $configuration[ConfigurationOptions::CHECKERS] = $normalizedConfiguration;

        return $configuration;
    }

    /**
     * @param mixed[] $configuration
     * @return mixed[]
     */
    private function prepareDefaults(array $configuration): array
    {
        if (! isset($configuration[ConfigurationOptions::CHECKERS])) {
            $configuration[ConfigurationOptions::CHECKERS] = [];
        }

        return $configuration;
    }

    /**
     * @param mixed[] $configuration
     */
    private function validateCheckerType(array $configuration): void
    {
        $this->checkerTypeValidator->validate(
            array_keys($configuration[ConfigurationOptions::CHECKERS])
        );
    }
}
