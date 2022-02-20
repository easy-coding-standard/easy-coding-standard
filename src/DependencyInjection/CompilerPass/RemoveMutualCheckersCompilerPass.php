<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowShortArraySyntaxSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\DisallowMultipleStatementsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowSpaceIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\NamespaceDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\UseDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\LowercaseClassKeywordsSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationArgumentSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\DoubleQuoteUsageSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer;
use PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\Whitespace\LineEndingFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder;
final class RemoveMutualCheckersCompilerPass implements \ECSPrefix20220220\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * List of checkers with the same functionality. If found, only the first one is used.
     *
     * @var string[][]
     */
    private const DUPLICATED_CHECKER_GROUPS = [
        [\PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff::class],
        [\PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowSpaceIndentSniff::class],
        [\PhpCsFixer\Fixer\Strict\StrictComparisonFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\Operators\\DisallowEqualOperatorsSniff'],
        [\PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\Classes\\ClassConstantVisibilitySniff'],
        [\PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowShortArraySyntaxSniff::class],
        [\PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff::class],
        [\PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer::class, \PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\LowercaseClassKeywordsSniff::class],
        [\PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff::class],
        [\PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer::class, \PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\UseDeclarationSniff::class],
        [\PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\Namespaces\\DisallowGroupUseSniff'],
        [\PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\Namespaces\\MultipleUsesPerLineSniff'],
        [\PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\TypeHints\\LongTypeHintsSniff'],
        [\PhpCsFixer\Fixer\Import\OrderedImportsFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\Namespaces\\AlphabeticallySortedUsesSniff'],
        [\PhpCsFixer\Fixer\Import\NoUnusedImportsFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\Namespaces\\UnusedUsesSniff'],
        [\PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\Arrays\\TrailingArrayCommaSniff'],
        [\PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\ControlStructures\\LanguageConstructWithParenthesesSniff'],
        [\PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\TypeHints\\ReturnTypeHintSpacingSniff'],
        [\PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\TypeHints\\ParameterTypeHintSpacingSniff'],
        [\PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer::class, \PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationArgumentSpacingSniff::class],
        [\PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\Commenting\\ForbiddenAnnotationsSniff'],
        [\PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer::class, \PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff::class],
        [\PhpCsFixer\Fixer\ControlStructure\IncludeFixer::class, \PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff::class],
        [\PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff::class, 'ECSPrefix20220220\\SlevomatCodingStandard\\Sniffs\\ControlStructures\\AssignmentInConditionSniff'],
        [\PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer::class, \PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\DoubleQuoteUsageSniff::class],
        // PSR2
        [\PhpCsFixer\Fixer\Basic\BracesFixer::class, \PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff::class],
        [\PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer::class, \PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\NamespaceDeclarationSniff::class],
        [\PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\DisallowMultipleStatementsSniff::class],
        [\PhpCsFixer\Fixer\Whitespace\LineEndingFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff::class],
        [\PhpCsFixer\Fixer\Casing\ConstantCaseFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff::class],
        [\PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff::class],
        [\PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer::class, \PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff::class],
        [\PhpCsFixer\Fixer\Basic\BracesFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff::class],
        [\PhpCsFixer\Fixer\Basic\BracesFixer::class, \PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff::class],
        [\PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer::class, \PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff::class],
        [\PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer::class, \PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff::class],
        [\PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer::class, \PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff::class],
    ];
    public function process(\ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : void
    {
        $checkersToRemove = $this->resolveCheckersToRemove($containerBuilder->getServiceIds());
        $definitions = $containerBuilder->getDefinitions();
        foreach ($definitions as $id => $definition) {
            if (\in_array($definition->getClass(), $checkersToRemove, \true)) {
                $containerBuilder->removeDefinition($id);
            }
        }
    }
    /**
     * @param string[] $checkers
     * @return string[]
     */
    private function resolveCheckersToRemove(array $checkers) : array
    {
        $checkers = \array_flip($checkers);
        $checkersToRemove = [];
        foreach (self::DUPLICATED_CHECKER_GROUPS as $matchingCheckerGroup) {
            if (!$this->isMatch($checkers, $matchingCheckerGroup)) {
                continue;
            }
            \array_shift($matchingCheckerGroup);
            $checkersToRemove = \array_merge($checkersToRemove, $matchingCheckerGroup);
        }
        return $checkersToRemove;
    }
    /**
     * @param string[] $checkers
     * @param string[] $matchingCheckerGroup
     */
    private function isMatch(array $checkers, array $matchingCheckerGroup) : bool
    {
        $matchingCheckerGroupKeys = \array_flip($matchingCheckerGroup);
        $matchingCheckers = \array_intersect_key($matchingCheckerGroupKeys, $checkers);
        return \count($matchingCheckers) === \count($matchingCheckerGroup);
    }
}
