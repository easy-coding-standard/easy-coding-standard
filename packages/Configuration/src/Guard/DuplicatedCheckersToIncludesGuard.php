<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Guard;
use Nette\Neon\Neon;

/**
 * Make sure their are no duplicates in "checkers" vs "includes"
 */
final class DuplicatedCheckersToIncludesGuard
{
    public function processConfigFile(string $configFile): void
    {
        $decodedFile = Neon::decode(file_get_contents($configFile));
        $checkers = $decodedFile['checkers'] ?? [];
        $includes = $decodedFile['includes'] ?? [];

        if (! $checkers || ! $includes) {
            return;
        }

        dump($checkers, $includes);
        die;
    }
}
