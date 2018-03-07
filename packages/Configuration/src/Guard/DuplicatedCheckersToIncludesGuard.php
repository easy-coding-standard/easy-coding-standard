<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Guard;

use Symfony\Component\Yaml\Yaml;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\Exception\Guard\DuplicatedCheckersLoadedException;
use Symplify\EasyCodingStandard\Configuration\Option;

/**
 * Make sure their are no duplicates in "checkers" vs "includes"
 */
final class DuplicatedCheckersToIncludesGuard
{
    /**
     * @var string
     */
    private const IMPORTS_KEY = 'imports';

    /**
     * @var CheckerConfigurationNormalizer
     */
    private $checkerConfigurationNormalizer;

    public function __construct(CheckerConfigurationNormalizer $checkerConfigurationNormalizer)
    {
        $this->checkerConfigurationNormalizer = $checkerConfigurationNormalizer;
    }

    public function processConfigFile(string $configFile): void
    {
        $decodedFile = Yaml::parse(file_get_contents($configFile));
        $mainCheckers = $decodedFile[Option::CHECKERS] ?? [];
        $includedCheckers = $this->resolveIncludedCheckers($configFile, $decodedFile);
        if (! $mainCheckers || ! $includedCheckers) {
            return;
        }

        $mainCheckers = $this->checkerConfigurationNormalizer->normalize($mainCheckers);
        $includedCheckers = $this->checkerConfigurationNormalizer->normalize($includedCheckers);

        $duplicateCheckersNames = $this->checkerArrayIntersect($mainCheckers, $includedCheckers);

        if (! $duplicateCheckersNames) {
            return;
        }

        throw new DuplicatedCheckersLoadedException(sprintf(
            'Duplicated checkers found in "%s" config: "%s". '
                . 'These checkers are already loaded in included configs with the same configuration.',
            $configFile,
            implode('", "', $duplicateCheckersNames)
        ));
    }

    /**
     * @param mixed[] $decodedFile
     * @return mixed[]
     */
    private function resolveIncludedCheckers(string $configFile, array $decodedFile): array
    {
        if (! isset($decodedFile[self::IMPORTS_KEY])) {
            return [];
        }

        $includedCheckers = [];
        foreach ($decodedFile[self::IMPORTS_KEY] as $includedFile) {
            // @todo validate 'resource'
            $includedFile = dirname($configFile) . DIRECTORY_SEPARATOR . $includedFile['resource'];
            // @todo validate file exists
            $includedCheckers = array_merge($includedCheckers, (array) Yaml::parse(file_get_contents($includedFile)));
        }

        return $includedCheckers[Option::CHECKERS] ?? [];
    }

    /**
     * @param mixed[] $mainCheckers
     * @param mixed[] $includedCheckers
     * @return mixed[]
     */
    private function checkerArrayIntersect(array $mainCheckers, array $includedCheckers): array
    {
        $sharedCheckerNames = $this->checkerNameIntersect($mainCheckers, $includedCheckers);

        $duplicateCheckersNames = [];

        foreach ($sharedCheckerNames as $sharedCheckerName) {
            $mainChecker = $mainCheckers[$sharedCheckerName];
            $includedChecker = $includedCheckers[$sharedCheckerName];

            // is their configuration different?
            if ($mainChecker !== $includedChecker) {
                continue;
            }

            $duplicateCheckersNames[] = $sharedCheckerName;
        }

        return $duplicateCheckersNames;
    }

    /**
     * @param mixed[] $mainCheckers
     * @param mixed[] $includedCheckers
     * @return string[]
     */
    private function checkerNameIntersect(array $mainCheckers, array $includedCheckers): array
    {
        $mainCheckerNames = array_keys($mainCheckers);
        $includedCheckerNames = array_keys($includedCheckers);

        return array_intersect($mainCheckerNames, $includedCheckerNames);
    }
}
