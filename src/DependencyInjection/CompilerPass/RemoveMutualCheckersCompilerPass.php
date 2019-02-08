<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RemoveMutualCheckersCompilerPass implements CompilerPassInterface
{
    /**
     * List of checkers with the same functionality.
     * If found, only the first one is used.
     *
     * @var string[][]
     */
    private static $duplicatedCheckerGroups = [
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
        ],                                               [
            'PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer',
            'PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\LowercaseClassKeywordsSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer',
            'PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\UseDeclarationSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer',
            'SlevomatCodingStandard\Sniffs\Namespaces\DisallowGroupUseSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer',
            'SlevomatCodingStandard\Sniffs\Namespaces\MultipleUsesPerLineSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer',
            'SlevomatCodingStandard\Sniffs\TypeHints\LongTypeHintsSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Import\OrderedImportsFixer',
            'SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Import\NoUnusedImportsFixer',
            'SlevomatCodingStandard\Sniffs\Namespaces\UnusedUses',
        ],                                               [
            'PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer',
            'SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff',
        ],                                               [
            'PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer',
            'SlevomatCodingStandard\Sniffs\ControlStructures\LanguageConstructWithParenthesesSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Basic\Psr4Fixer',
            'SlevomatCodingStandard\Sniffs\Files\TypeNameMatchesFileNameSniff',
        ],                                               [
            'PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer',
            'SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSpacingSniff',
        ],                                               [
            'PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer',
            'SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSpacingSniff',
        ],                                               [
            'PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer',
            'PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationArgumentSpacingSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer',
            'SlevomatCodingStandard\Sniffs\Commenting\ForbiddenAnnotationsSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Whitespace\NoExtraConsecutiveBlankLinesFixer',
            'PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer',
            'PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff',
        ],                                               [
            'PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff',
            'PhpCsFixer\Fixer\ControlStructure\IncludeFixer',
        ],                                               [
            'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff',
            'SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff',
        ],                                               [
            'PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer',
            'PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\DoubleQuoteUsageSniff',
        ],
        // PSR2
        [
            'PhpCsFixer\Fixer\Basic\BracesFixer',
            'PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff',
        ],                                               [
            'PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer',
            'PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\NamespaceDeclarationSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\DisallowMultipleStatementsSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Whitespace\LineEndingFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Casing\LowercaseConstantsFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer',
            'PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Basic\BracesFixer',
            'PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff',
        ],                                               [
            'PhpCsFixer\Fixer\Basic\BracesFixer',
            'PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ScopeClosingBraceSniff',
        ],                                               [
            'PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer',
            'PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff',
        ],                                               [
            'PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer',
            'PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff',
        ],                                               [
            'PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer',
            'PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff',
        ],
        // Aliased deprecated fixers
        [
            'PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer',
            'PhpCsFixer\Fixer\Whitespace\NoExtraConsecutiveBlankLinesFixer',
        ],
        [
            'PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer',
            'PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer',
        ],
        ['PhpCsFixer\Fixer\Operator\IncrementStyleFixer', 'PhpCsFixer\Fixer\Operator\PreIncrementFixer'],
        [
            'PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer',
            'PhpCsFixer\Fixer\Comment\HashToSlashCommentFixer',
        ],
    ];

    public function process(ContainerBuilder $containerBuilder): void
    {
        $checkersToRemove = $this->resolveCheckersToRemove($containerBuilder->getServiceIds());

        foreach ($containerBuilder->getDefinitions() as $id => $definition) {
            if (in_array($definition->getClass(), $checkersToRemove, true)) {
                $containerBuilder->removeDefinition($id);
            }
        }
    }

    /**
     * @param string[] $checkers
     * @return string[]
     */
    private function resolveCheckersToRemove(array $checkers): array
    {
        $checkers = (array) array_flip($checkers);

        $checkersToRemove = [];
        foreach (self::$duplicatedCheckerGroups as $matchingCheckerGroup) {
            if (! $this->isMatch($checkers, $matchingCheckerGroup)) {
                continue;
            }

            array_shift($matchingCheckerGroup);
            foreach ($matchingCheckerGroup as $checkerToRemove) {
                $checkersToRemove[] = $checkerToRemove;
            }
        }

        return $checkersToRemove;
    }

    /**
     * @param string[] $checkers
     * @param string[] $matchingCheckerGroup
     */
    private function isMatch(array $checkers, array $matchingCheckerGroup): bool
    {
        $matchingCheckerGroupKeys = array_flip($matchingCheckerGroup);

        return count(array_intersect_key($matchingCheckerGroupKeys, $checkers)) === count($matchingCheckerGroup);
    }
}
