<?php
declare(strict_types=1);

use PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer;
use PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer;
use PhpCsFixer\Fixer\Casing\LowercaseStaticReferenceFixer;
use PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer;
use PhpCsFixer\Fixer\Casing\MagicMethodCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionTypeDeclarationCasingFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\CastNotation\LowercaseCastFixer;
use PhpCsFixer\Fixer\CastNotation\NoShortBoolCastFixer;
use PhpCsFixer\Fixer\CastNotation\NoUnsetCastFixer;
use PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\NoNullPropertyInitializationFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleTraitInsertPerStatementFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\MultilineCommentOpeningClosingFixer;
use PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer;
use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ControlStructure\ElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\ControlStructure\NoAlternativeSyntaxFixer;
use PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\NoTrailingCommaInListCallFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer;
use PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\LanguageConstruct\ExplicitIndirectVariableFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Operator\NewWithBracesFixer;
use PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer;
use PhpCsFixer\Fixer\Operator\StandardizeNotEqualsFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\AlignMultilineCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAnnotationWithoutDotFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocInlineTagFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAliasTagFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoUselessInheritdocFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocReturnSelfReferenceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarAnnotationCorrectOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer;
use PhpCsFixer\Fixer\PhpTag\NoShortEchoTagFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitFqcnAnnotationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitInternalClassFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitOrderedCoversFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\SemicolonAfterInstructionFixer;
use PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer;
use PhpCsFixer\Fixer\StringNotation\EscapeImplicitBackslashesFixer;
use PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer;
use PhpCsFixer\Fixer\StringNotation\HeredocToNowdocFixer;
use PhpCsFixer\Fixer\StringNotation\NoBinaryStringFixer;
use PhpCsFixer\Fixer\StringNotation\SimpleToComplexStringVariableFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\Whitespace\CompactNullableTypehintFixer;
use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\Whitespace\LineEndingFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer;
use PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer;
use PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(AlignMultilineCommentFixer::class);
    $services->set(ArrayIndentationFixer::class);
    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [[
            'syntax' => 'short',
        ]]);
    $services->set(BinaryOperatorSpacesFixer::class);
    $services->set(BlankLineAfterNamespaceFixer::class);
    $services->set(BlankLineAfterOpeningTagFixer::class);
    $services->set(BlankLineBeforeStatementFixer::class);
    $services->set(BracesFixer::class)
        ->call('configure', [[
            'allow_single_line_closure' => true,
        ]]);
    $services->set(CastSpacesFixer::class);
    $services->set(ClassAttributesSeparationFixer::class)
        ->call('configure', [[
            'elements' => ['method'],
        ]]);
    $services->set(ClassDefinitionFixer::class)
        ->call('configure', [[
            'single_line' => true,
        ]]);
    $services->set(CombineConsecutiveIssetsFixer::class);
    $services->set(CombineConsecutiveUnsetsFixer::class);
    $services->set(CompactNullableTypehintFixer::class);
    $services->set(ConcatSpaceFixer::class);
    $services->set(ConstantCaseFixer::class);
    $services->set(DeclareEqualNormalizeFixer::class);
    $services->set(ElseifFixer::class);
    $services->set(EncodingFixer::class);
    $services->set(EscapeImplicitBackslashesFixer::class);
    $services->set(ExplicitIndirectVariableFixer::class);
    $services->set(ExplicitStringVariableFixer::class);
    $services->set(FullOpeningTagFixer::class);
    $services->set(FullyQualifiedStrictTypesFixer::class);
    $services->set(FunctionDeclarationFixer::class);
    $services->set(FunctionTypehintSpaceFixer::class);
    $services->set(HeredocToNowdocFixer::class);
    $services->set(IncludeFixer::class);
    $services->set(IncrementStyleFixer::class);
    $services->set(IndentationTypeFixer::class);
    $services->set(LineEndingFixer::class);
    $services->set(LowercaseCastFixer::class);
    $services->set(LowercaseKeywordsFixer::class);
    $services->set(LowercaseStaticReferenceFixer::class);
    $services->set(MagicConstantCasingFixer::class);
    $services->set(MagicMethodCasingFixer::class);
    $services->set(MethodArgumentSpaceFixer::class)
        ->call('configure', [[
            'on_multiline' => 'ensure_fully_multiline',
        ]]);
    $services->set(MethodChainingIndentationFixer::class);
    $services->set(MultilineCommentOpeningClosingFixer::class);
    $services->set(MultilineWhitespaceBeforeSemicolonsFixer::class)
        ->call('configure', [[
            'strategy' => 'new_line_for_chained_calls',
        ]]);
    $services->set(NativeFunctionCasingFixer::class);
    $services->set(NativeFunctionTypeDeclarationCasingFixer::class);
    $services->set(NewWithBracesFixer::class);
    $services->set(NoAlternativeSyntaxFixer::class);
    $services->set(NoBinaryStringFixer::class);
    $services->set(NoBlankLinesAfterClassOpeningFixer::class);
    $services->set(NoBlankLinesAfterPhpdocFixer::class);
    $services->set(NoBreakCommentFixer::class);
    $services->set(NoClosingTagFixer::class);
    $services->set(NoEmptyCommentFixer::class);
    $services->set(NoEmptyPhpdocFixer::class);
    $services->set(NoEmptyStatementFixer::class);
    $services->set(NoExtraBlankLinesFixer::class)
        ->call('configure', [[
            'tokens' => [
                'break',
                'continue',
                'curly_brace_block',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'throw',
                'use',
            ],
        ]]);
    $services->set(NoLeadingImportSlashFixer::class);
    $services->set(NoLeadingNamespaceWhitespaceFixer::class);
    $services->set(NoMixedEchoPrintFixer::class);
    $services->set(NoMultilineWhitespaceAroundDoubleArrowFixer::class);
    $services->set(NoNullPropertyInitializationFixer::class);
    $services->set(NoShortBoolCastFixer::class);
    $services->set(NoShortEchoTagFixer::class);
    $services->set(NoSinglelineWhitespaceBeforeSemicolonsFixer::class);
    $services->set(NoSpacesAfterFunctionNameFixer::class);
    $services->set(NoSpacesAroundOffsetFixer::class);
    $services->set(NoSpacesInsideParenthesisFixer::class);
    $services->set(NoSuperfluousElseifFixer::class);
    $services->set(NoSuperfluousPhpdocTagsFixer::class)
        ->call('configure', [[
            'allow_mixed' => true,
            'allow_unused_params' => true,
        ]]);
    $services->set(NoTrailingCommaInListCallFixer::class);
    $services->set(NoTrailingCommaInSinglelineArrayFixer::class);
    $services->set(NoTrailingWhitespaceFixer::class);
    $services->set(NoTrailingWhitespaceInCommentFixer::class);
    $services->set(NoUnneededControlParenthesesFixer::class);
    $services->set(NoUnneededCurlyBracesFixer::class)
        ->call('configure', [[
            'namespaces' => true,
        ]]);
    $services->set(NoUnsetCastFixer::class);
    $services->set(NoUnusedImportsFixer::class);
    $services->set(NoUselessElseFixer::class);
    $services->set(NoUselessReturnFixer::class);
    $services->set(NoWhitespaceBeforeCommaInArrayFixer::class);
    $services->set(NoWhitespaceInBlankLineFixer::class);
    $services->set(NormalizeIndexBraceFixer::class);
    $services->set(ObjectOperatorWithoutWhitespaceFixer::class);
    $services->set(OrderedClassElementsFixer::class);
    $services->set(OrderedImportsFixer::class);
    $services->set(PhpUnitFqcnAnnotationFixer::class);
    $services->set(PhpUnitInternalClassFixer::class);
    $services->set(PhpUnitMethodCasingFixer::class);
    $services->set(PhpUnitOrderedCoversFixer::class);
    $services->set(PhpUnitTestClassRequiresCoversFixer::class);
    $services->set(PhpdocAddMissingParamAnnotationFixer::class);
    $services->set(PhpdocAlignFixer::class)
        ->call('configure', [[
            'tags' => ['method', 'param', 'property', 'return', 'throws', 'type', 'var'],
        ]]);
    $services->set(PhpdocAnnotationWithoutDotFixer::class);
    $services->set(PhpdocIndentFixer::class);
    $services->set(PhpdocInlineTagFixer::class);
    $services->set(PhpdocNoAccessFixer::class);
    $services->set(PhpdocNoAliasTagFixer::class);
    $services->set(PhpdocNoEmptyReturnFixer::class);
    $services->set(PhpdocNoPackageFixer::class);
    $services->set(PhpdocNoUselessInheritdocFixer::class);
    $services->set(PhpdocOrderFixer::class);
    $services->set(PhpdocReturnSelfReferenceFixer::class);
    $services->set(PhpdocScalarFixer::class);
    $services->set(PhpdocSeparationFixer::class);
    $services->set(PhpdocSingleLineVarSpacingFixer::class);
    $services->set(PhpdocSummaryFixer::class);
    $services->set(PhpdocToCommentFixer::class);
    $services->set(PhpdocTrimFixer::class);
    $services->set(PhpdocTrimConsecutiveBlankLineSeparationFixer::class);
    $services->set(PhpdocTypesFixer::class);
    $services->set(PhpdocTypesOrderFixer::class);
    $services->set(PhpdocVarAnnotationCorrectOrderFixer::class);
    $services->set(PhpdocVarWithoutNameFixer::class);
    $services->set(ProtectedToPrivateFixer::class);
    $services->set(ReturnAssignmentFixer::class);
    $services->set(ReturnTypeDeclarationFixer::class);
    $services->set(SemicolonAfterInstructionFixer::class);
    $services->set(ShortScalarCastFixer::class);
    $services->set(SimpleToComplexStringVariableFixer::class);
    $services->set(SingleBlankLineAtEofFixer::class);
    $services->set(SingleBlankLineBeforeNamespaceFixer::class);
    $services->set(SingleClassElementPerStatementFixer::class);
    $services->set(SingleImportPerStatementFixer::class);
    $services->set(SingleLineAfterImportsFixer::class);
    $services->set(SingleLineCommentStyleFixer::class);
    $services->set(SingleQuoteFixer::class);
    $services->set(SingleTraitInsertPerStatementFixer::class);
    $services->set(SpaceAfterSemicolonFixer::class)
        ->call('configure', [[
            'remove_in_empty_for_expressions' => true,
        ]]);
    $services->set(StandardizeIncrementFixer::class);
    $services->set(StandardizeNotEqualsFixer::class);
    $services->set(SwitchCaseSemicolonToColonFixer::class);
    $services->set(SwitchCaseSpaceFixer::class);
    $services->set(TernaryOperatorSpacesFixer::class);
    $services->set(TrailingCommaInMultilineArrayFixer::class);
    $services->set(TrimArraySpacesFixer::class);
    $services->set(UnaryOperatorSpacesFixer::class);
    $services->set(VisibilityRequiredFixer::class);
    $services->set(WhitespaceAfterCommaInArrayFixer::class);
    $services->set(YodaStyleFixer::class);
};
