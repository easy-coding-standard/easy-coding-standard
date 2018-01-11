<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Guard;
use Nette\DI\Config\Helpers;
use Nette\DI\Config\Loader;
use Nette\Neon\Neon;
use Symplify\EasyCodingStandard\Configuration\Exception\Guard\DuplicatedCheckersLoadedException;
use Symplify\PackageBuilder\Neon\Loader\NeonLoader;

/**
 * Make sure their are no duplicates in "checkers" vs "includes"
 */
final class DuplicatedCheckersToIncludesGuard
{
    public function processConfigFile(string $configFile): void
    {
        $decodedFile = Neon::decode(file_get_contents($configFile));

        $mainCheckers = $decodedFile['checkers'] ?? [];
        $includedCheckers = $this->resolveIncludedCheckers($configFile);

        if (! $mainCheckers || ! $includedCheckers) {
            return;
        }

        $duplicatedCheckers = array_intersect($mainCheckers, $includedCheckers);
        if (! $duplicatedCheckers) {
            return;
        }

        dump($duplicatedCheckers);

        throw new DuplicatedCheckersLoadedException(sprintf(
            'Duplicated checkers found in "%s" config: "%s". '
                . 'These checkers are alread loaded in included configs with the same configuration.',
            $configFile,
            implode('", "', $duplicatedCheckers)
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
}
