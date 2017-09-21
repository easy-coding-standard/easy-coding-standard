<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use Symplify\EasyCodingStandard\Configuration\Exception\ConflictingCheckersLoadedException;

final class ConflictingCheckerGuard
{
    /**
     * These groups do the opposite of each other, e.g. Yoda vs NoYoda.
     *
     * @var string[][]
     */
    private static $conflictingCheckerGroups = [
        [
            'SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff',
            'PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer',
        ], [
            'PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer',
            'PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer',
        ], [
            'Symplify\CodingStandard\Sniffs\ControlStructures\NewClassSniff',
            'PhpCsFixer\Fixer\Operator\NewWithBracesFixer',
        ], [
            'SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff',
            'PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer',
        ], [
            'SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff',
            'PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer',
        ],
    ];

    /**
     * @param mixed[] $checkers
     */
    public function processCheckers(array $checkers): void
    {
        foreach (self::$conflictingCheckerGroups as $viceVersaMatchingCheckerGroup) {
            if (! $this->isMatch($checkers, $viceVersaMatchingCheckerGroup)) {
                continue;
            }

            throw new ConflictingCheckersLoadedException(sprintf(
                'Checkers "%s" mutually exclude each other. Use only one or exclude '
                . 'the unwanted one in "parameters > exclude_checkers" in your config.',
                implode('" and "', $viceVersaMatchingCheckerGroup)
            ));
        }
    }

    /**
     * @param mixed[] $checkers
     * @param string[] $matchingCheckerGroup
     */
    private function isMatch(array $checkers, array $matchingCheckerGroup): bool
    {
        $matchingCheckerGroupKeys = array_flip($matchingCheckerGroup);

        return count(array_intersect_key($matchingCheckerGroupKeys, $checkers)) === count($matchingCheckerGroup);
    }
}
