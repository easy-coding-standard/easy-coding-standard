<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Guard;
use Nette\DI\Config\Helpers;
use Nette\DI\Config\Loader;
use Nette\Neon\Neon;
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

        // @todo compare
        dump($mainCheckers);
        dump($includedCheckers);
        die;
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
