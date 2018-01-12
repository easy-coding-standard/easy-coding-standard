<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Guard;

use Nette\DI\Config\Helpers;
use Nette\DI\Config\Loader;
use Nette\Neon\Neon;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\Exception\Guard\DuplicatedCheckersLoadedException;
use Symplify\EasyCodingStandard\Configuration\Option;

/**
 * Make sure their are no duplicates in "checkers" vs "includes"
 */
final class DuplicatedCheckersToIncludesGuard
{
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
        $decodedFile = Neon::decode(file_get_contents($configFile));
        $mainCheckers = $decodedFile[Option::CHECKERS] ?? [];
        $includedCheckers = $this->resolveIncludedCheckers($configFile);
        if (! $mainCheckers || ! $includedCheckers) {
            return;
        }

        $mainCheckers = $this->checkerConfigurationNormalizer->normalize($mainCheckers);
        $includedCheckers = $this->checkerConfigurationNormalizer->normalize($includedCheckers);

        $duplicatedCheckers = $this->arrayIntersectNested($mainCheckers, $includedCheckers);
        if (! $duplicatedCheckers) {
            return;
        }

        $duplicateCheckersNames = array_keys($duplicatedCheckers);

        dump($duplicateCheckersNames);

        throw new DuplicatedCheckersLoadedException(sprintf(
            'Duplicated checkers found in "%s" config: "%s". '
                . 'These checkers are alread loaded in included configs with the same configuration.',
            $configFile,
            implode('", "', $duplicateCheckersNames)
        ));
    }

    /**
     * @return mixed[]
     */
    private function resolveIncludedCheckers(string $configFile): array
    {
        $neonLoader = new Loader();
        $neonLoader->load($configFile);
        $allFiles = $neonLoader->getDependencies();

        unset($allFiles[0]); // removes main file

        $includedCheckers = [];
        foreach ($allFiles as $includedFile) {
            $includedCheckers = Helpers::merge($includedCheckers, $neonLoader->load($includedFile));
        }

        return $includedCheckers['checkers'] ?? [];
    }

    /**
     * @param mixed[] $firstArray
     * @param mixed[] $secondArray
     * @return mixed[]
     */
    private function arrayIntersectNested(array $firstArray, array $secondArray): array
    {
        dump($firstArray, $secondArray);

        return array_uintersect($firstArray, $secondArray, function (array $firstArray, array $secondArray): int {

            dump($firstArray, $secondArray);

            return $firstArray === $secondArray ? 0 : -1;
        });
    }
}
