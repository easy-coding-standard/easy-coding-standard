<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;

final class MutualCheckerExcluder
{
    /**
     * List of checkers with the same functionality.
     * If found, only the first one is used.
     *
     * @var string[][]
     */
    private $matchingCheckerGroups = [
        [
            'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff',
            'Symplify\CodingStandard\Sniffs\Commenting\VarPropertyCommentSniff',
        ],
        [
            'SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff',
            'Symplify\CodingStandard\Sniffs\Namespaces\ClassNamesWithoutPreSlashSniff',
        ],
    ];

    /**
     * @param Sniff[]|FixerInterface[] $checkers
     * @return Sniff[]|FixerInterface[]
     */
    public function exclude(array $checkers): array
    {
        foreach ($this->matchingCheckerGroups as $matchingCheckerGroup) {
            if (! $this->isMatch($checkers, $matchingCheckerGroup)) {
                continue;
            }

            array_shift($matchingCheckerGroup);
            foreach ($matchingCheckerGroup as $checkerToRemove) {
                $keyToRemove = array_search($checkerToRemove, $checkers, true);
                unset($checkers[$keyToRemove]);
            }
        }

        return $checkers;
    }

    /**
     * @param mixed[] $checkers
     * @param mixed[] $matchingCheckerGroup
     */
    private function isMatch(array $checkers, array $matchingCheckerGroup): bool
    {
        return count(array_intersect_key($matchingCheckerGroup, $checkers)) === count($matchingCheckerGroup);
    }
}
