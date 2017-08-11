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
        ],
    ];

    /**
     * @param mixed[][] $checkers
     * @return mixed[][]
     */
    public function exclude(array $checkers): array
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
     * @param mixed[] $checkers
     * @param string[] $matchingCheckerGroup
     */
    private function isMatch(array $checkers, array $matchingCheckerGroup): bool
    {
        $matchingCheckerGroupKeys = array_flip($matchingCheckerGroup);

        return count(array_intersect_key($matchingCheckerGroupKeys, $checkers)) === count($matchingCheckerGroup);
    }
}
