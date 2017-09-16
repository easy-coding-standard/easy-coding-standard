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
    private static $matchingCheckerGroups = [
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
        ], [
            'PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer',
            'PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\LowercaseClassKeywordsSniff',
        ], [
            'PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff',
        ], [
            'PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer',
            'PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\UseDeclarationSniff',
        ], [
            'PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer',
            'SlevomatCodingStandard\Sniffs\Namespaces\DisallowGroupUseSniff',
        ], [
            'PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer',
            'SlevomatCodingStandard\Sniffs\Namespaces\MultipleUsesPerLineSniff',
        ], [
            'PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer',
            'SlevomatCodingStandard\Sniffs\TypeHints\LongTypeHintsSniff',
        ], [
            'PhpCsFixer\Fixer\Import\OrderedImportsFixer',
            'SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff',
        ], [
            'PhpCsFixer\Fixer\Import\NoUnusedImportsFixer',
            'SlevomatCodingStandard\Sniffs\Namespaces\UnusedUses',
        ], [
            'PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer',
            'SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff',
        ], [
            'PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer',
            'SlevomatCodingStandard\Sniffs\ControlStructures\LanguageConstructWithParenthesesSniff',
        ], [
            'PhpCsFixer\Fixer\Basic\Psr4Fixer',
            'SlevomatCodingStandard\Sniffs\Files\TypeNameMatchesFileNameSniff',
        ], [
            'PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer',
            'SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSpacingSniff',
        ], [
            'PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer',
            'SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSpacingSniff',
        ], [
            'PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer',
            'PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationArgumentSpacingSniff',
        ], [
            'PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer',
            'SlevomatCodingStandard\Sniffs\Commenting\ForbiddenAnnotationsSniff',
        ], [
            'PhpCsFixer\Fixer\Whitespace\NoExtraConsecutiveBlankLinesFixer',
            'PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff',
        ],
        // PSR2
        [
            'PhpCsFixer\Fixer\Basic\BracesFixer',
            'PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff',
        ], [
            'PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer',
            'PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\NamespaceDeclarationSniff',
        ], [
            'PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\DisallowMultipleStatementsSniff',
        ], [
            'PhpCsFixer\Fixer\Whitespace\LineEndingFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff',
        ], [
            'PhpCsFixer\Fixer\Casing\LowercaseConstantsFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff',
        ], [
            'PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff',
        ], [
            'PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer',
            'PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff',
        ], [
            'PhpCsFixer\Fixer\Basic\BracesFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff',
        ],
    ];

    /**
     * These groups do the opposite of each other, e.g. Yoda vs NoYoda.
     *
     * @var string[][]
     */
    private static $viceVersaMatchingCheckerGroups = [
        [
            'SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff',
            'PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer',
        ], [
            'PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer',
            'PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer',
        ], [
            'Symplify\CodingStandard\Sniffs\ControlStructures\NewClassSniff',
            'PhpCsFixer\Fixer\Operator\NewWithBracesFixer',
        ],
    ];

    /**
     * @param mixed[][] $checkers
     * @return mixed[][]
     */
    public function exclude(array $checkers): array
    {
        $checkers = $this->excludeDuplicatedGroups($checkers);

        return $this->excludeViseVersaGroups($checkers);
    }

    /**
     * @param mixed[] $checkers
     * @return mixed[]
     */
    private function excludeDuplicatedGroups(array $checkers): array
    {
        foreach (self::$matchingCheckerGroups as $matchingCheckerGroup) {
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
     * If matched, use only last checkers. That allows overriding general configs.
     *
     * @param mixed[] $checkers
     * @return mixed[]
     */
    private function excludeViseVersaGroups(array $checkers): array
    {
        foreach (self::$viceVersaMatchingCheckerGroups as $viceVersaMatchingCheckerGroup) {
            if (! $this->isMatch($checkers, $viceVersaMatchingCheckerGroup)) {
                continue;
            }

            [$firstCheckerClass, $secondCheckerClass] = $viceVersaMatchingCheckerGroup;

            $firstCheckerPosition = array_search($firstCheckerClass, array_keys($checkers), true);
            $secondCheckerPosition = array_search($secondCheckerClass, array_keys($checkers), true);

            $checkerClassToRemove = ($firstCheckerPosition < $secondCheckerPosition)
                ? $firstCheckerClass : $secondCheckerClass;

            unset($checkers[$checkerClassToRemove]);
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
