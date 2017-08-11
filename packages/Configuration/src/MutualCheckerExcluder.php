<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

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
        [
            'PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff',
        ],
        [
            'PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowSpaceIndentSniff',
        ],
        [
            'PhpCsFixer\Fixer\Strict\StrictComparisonFixer',
            'SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEqualOperatorsSniff',
        ],
        [
            'PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer',
            'SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff',
        ],
        [
            'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff',
            'Symplify\CodingStandard\Sniffs\Commenting\MethodCommentSniff',
        ],
        [
            'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff',
            'Symplify\CodingStandard\Sniffs\Commenting\MethodReturnTypeSniff',
        ],
        [
            'PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowShortArraySyntaxSniff',
        ],
        [
            'PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff',
        ],
    ];

    /**
     * @param mixed[][] $checkers
     * @return mixed[][]
     */
    public function exclude(array $checkers): array
    {
        foreach ($this->matchingCheckerGroups as $matchingCheckerGroup) {
            if (! $this->isMatch($checkers, $matchingCheckerGroup)) {
                continue;
            }

            array_shift($matchingCheckerGroup);
            foreach ($matchingCheckerGroup as $checkerToRemove) {
                unset($checkers[$checkerToRemove]);
            }
        }

        return $checkers;
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
