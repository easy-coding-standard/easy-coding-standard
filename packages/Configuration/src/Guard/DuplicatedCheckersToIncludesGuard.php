<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Guard;
use Nette\DI\Config\Helpers;
use Nette\DI\Config\Loader;
use Nette\Neon\Neon;
use Nette\Utils\Arrays;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\Exception\Guard\DuplicatedCheckersLoadedException;
use Symplify\PackageBuilder\Neon\Loader\NeonLoader;

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

        $mainCheckers = $decodedFile['checkers'] ?? [];
        $includedCheckers = $this->resolveIncludedCheckers($configFile);

        if (! $mainCheckers || ! $includedCheckers) {
            return;
        }

        $mainCheckers = $this->checkerConfigurationNormalizer->normalize($mainCheckers);
        $includedCheckers = $this->checkerConfigurationNormalizer->normalize($includedCheckers);

        // normalize both and make deep diff (Nette\Arrays?)
        $duplicatedCheckers = array_uintersect($mainCheckers, $includedCheckers, function ($firstArray, $secondArray): int {
            if ($firstArray === $secondArray) {
                return 0;
            }

            return array_intersect($firstArray, $secondArray) ? 1 : -1;
        });

        if (! $duplicatedCheckers) {
            return;
        }

        $duplicateCheckersNames = array_keys($duplicatedCheckers);

        throw new DuplicatedCheckersLoadedException(sprintf(
            'Duplicated checkers found in "%s" config: "%s". '
                . 'These checkers are alread loaded in included configs with the same configuration.',
            $configFile,
            implode('", "', $duplicateCheckersNames)
        ));
    }

    private function array_intersect_assoc_recursive(&$arr1, &$arr2) {
        if (!is_array($arr1) || !is_array($arr2)) {
            return (string) $arr1 == (string) $arr2;
        }
        $commonkeys = array_intersect(array_keys($arr1), array_keys($arr2));
        $ret = array();
        foreach ($commonkeys as $key) {
            $result = $this->array_intersect_assoc_recursive($arr1[$key], $arr2[$key]);;
            $ret[$key] = &$result;
        }
        return $ret;
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
}
