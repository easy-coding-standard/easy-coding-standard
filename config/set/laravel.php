<?php

declare (strict_types=1);
namespace ECSPrefix202408;

use PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer;
use PhpCsFixer\Fixer\Alias\NoAliasLanguageConstructCallFixer;
use PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer;
use PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\Basic\CurlyBracesPositionFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\Basic\NoMultipleStatementsPerLineFixer;
use PhpCsFixer\Fixer\Basic\NoTrailingCommaInSinglelineFixer;
use PhpCsFixer\Fixer\Basic\PsrAutoloadingFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\Casing\IntegerLiteralCaseFixer;
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
use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\ClassNotation\SelfStaticAccessorFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ControlStructure\ControlStructureBracesFixer;
use PhpCsFixer\Fixer\ControlStructure\ControlStructureContinuationPositionFixer;
use PhpCsFixer\Fixer\ControlStructure\ElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\ControlStructure\NoAlternativeSyntaxFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\LambdaNotUsedImportFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoUnreachableDefaultArgumentValueFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer;
use PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareParenthesesFixer;
use PhpCsFixer\Fixer\LanguageConstruct\SingleSpaceAroundConstructFixer;
use PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\CleanNamespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Operator\NoSpaceAroundDoubleColonFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\StandardizeNotEqualsFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocTagRenameFixer;
use PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocInlineTagNormalizerFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoUselessInheritdocFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTagTypeFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\ReturnNotation\SimplifiedNullReturnFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer;
use PhpCsFixer\Fixer\StringNotation\HeredocToNowdocFixer;
use PhpCsFixer\Fixer\StringNotation\NoBinaryStringFixer;
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
use PhpCsFixer\Fixer\Whitespace\StatementIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\TypesSpacesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
/**
 * Ruleset converted from Laravel Pint
 * @see https://github.com/laravel/pint/blob/main/resources/presets/laravel.php
 * Rule order should be kept alphabetical
 * Note: does not include Laravel's custom LaravelPhpdocAlignmentFixer
 */
return ECSConfig::configure()->withRules([ArrayIndentationFixer::class, BlankLineAfterNamespaceFixer::class, BlankLineAfterOpeningTagFixer::class, CastSpacesFixer::class, CleanNamespaceFixer::class, CompactNullableTypehintFixer::class, ControlStructureBracesFixer::class, DeclareEqualNormalizeFixer::class, DeclareParenthesesFixer::class, ElseifFixer::class, EncodingFixer::class, FullOpeningTagFixer::class, FullyQualifiedStrictTypesFixer::class, FunctionDeclarationFixer::class, FunctionTypehintSpaceFixer::class, GeneralPhpdocTagRenameFixer::class, HeredocToNowdocFixer::class, IncludeFixer::class, IndentationTypeFixer::class, IntegerLiteralCaseFixer::class, LambdaNotUsedImportFixer::class, LinebreakAfterOpeningTagFixer::class, LineEndingFixer::class, ListSyntaxFixer::class, LowercaseCastFixer::class, LowercaseKeywordsFixer::class, LowercaseStaticReferenceFixer::class, MagicMethodCasingFixer::class, MagicConstantCasingFixer::class, NativeFunctionCasingFixer::class, NativeFunctionTypeDeclarationCasingFixer::class, NoAliasFunctionsFixer::class, NoAliasLanguageConstructCallFixer::class, NoAlternativeSyntaxFixer::class, NoBinaryStringFixer::class, NoBlankLinesAfterClassOpeningFixer::class, NoBlankLinesAfterPhpdocFixer::class, NoClosingTagFixer::class, NoEmptyPhpdocFixer::class, NoEmptyStatementFixer::class, MethodChainingIndentationFixer::class, NoMultilineWhitespaceAroundDoubleArrowFixer::class, NoMultipleStatementsPerLineFixer::class, NoShortBoolCastFixer::class, NoSinglelineWhitespaceBeforeSemicolonsFixer::class, NoSpacesAfterFunctionNameFixer::class, NoSpaceAroundDoubleColonFixer::class, NoLeadingImportSlashFixer::class, NoLeadingNamespaceWhitespaceFixer::class, NoSpacesInsideParenthesisFixer::class, NoTrailingCommaInSinglelineFixer::class, NoTrailingWhitespaceFixer::class, NoTrailingWhitespaceInCommentFixer::class, NoUnneededCurlyBracesFixer::class, NoUnreachableDefaultArgumentValueFixer::class, NoUnsetCastFixer::class, NoUnusedImportsFixer::class, NoUselessReturnFixer::class, NoWhitespaceBeforeCommaInArrayFixer::class, NoWhitespaceInBlankLineFixer::class, NormalizeIndexBraceFixer::class, NotOperatorWithSuccessorSpaceFixer::class, ObjectOperatorWithoutWhitespaceFixer::class, PhpdocIndentFixer::class, PhpdocInlineTagNormalizerFixer::class, PhpdocNoAccessFixer::class, PhpdocNoPackageFixer::class, PhpdocNoUselessInheritdocFixer::class, PhpdocScalarFixer::class, PhpdocSingleLineVarSpacingFixer::class, PhpdocTrimFixer::class, PhpdocTypesFixer::class, PhpdocVarWithoutNameFixer::class, SelfStaticAccessorFixer::class, ShortScalarCastFixer::class, SingleBlankLineAtEofFixer::class, SingleBlankLineBeforeNamespaceFixer::class, SingleImportPerStatementFixer::class, SingleLineAfterImportsFixer::class, SingleQuoteFixer::class, SingleSpaceAroundConstructFixer::class, SpaceAfterSemicolonFixer::class, StandardizeNotEqualsFixer::class, SwitchCaseSemicolonToColonFixer::class, SwitchCaseSpaceFixer::class, TernaryOperatorSpacesFixer::class, WhitespaceAfterCommaInArrayFixer::class, TrimArraySpacesFixer::class, TypesSpacesFixer::class, UnaryOperatorSpacesFixer::class])->withConfiguredRule(ArraySyntaxFixer::class, ['syntax' => 'short'])->withConfiguredRule(BinaryOperatorSpacesFixer::class, ['default' => 'single_space'])->withConfiguredRule(BlankLineBeforeStatementFixer::class, ['statements' => ['continue', 'return']])->withConfiguredRule(ClassAttributesSeparationFixer::class, ['elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one', 'trait_import' => 'none']])->withConfiguredRule(ClassDefinitionFixer::class, ['multi_line_extends_each_single_line' => \true, 'single_item_single_line' => \true, 'single_line' => \true])->withConfiguredRule(ConcatSpaceFixer::class, ['spacing' => 'none'])->withConfiguredRule(ConstantCaseFixer::class, ['case' => 'lower'])->withConfiguredRule(ControlStructureContinuationPositionFixer::class, ['position' => 'same_line'])->withConfiguredRule(CurlyBracesPositionFixer::class, ['control_structures_opening_brace' => 'same_line', 'functions_opening_brace' => 'next_line_unless_newline_at_signature_end', 'anonymous_functions_opening_brace' => 'same_line', 'classes_opening_brace' => 'next_line_unless_newline_at_signature_end', 'anonymous_classes_opening_brace' => 'next_line_unless_newline_at_signature_end', 'allow_single_line_empty_anonymous_classes' => \false, 'allow_single_line_anonymous_functions' => \false])->withConfiguredRule(IncrementStyleFixer::class, ['style' => 'post'])->withConfiguredRule(MethodArgumentSpaceFixer::class, ['on_multiline' => 'ignore'])->withConfiguredRule(MultilineWhitespaceBeforeSemicolonsFixer::class, ['strategy' => 'no_multi_line'])->withConfiguredRule(NoExtraBlankLinesFixer::class, ['tokens' => ['extra', 'throw', 'use']])->withConfiguredRule(NoMixedEchoPrintFixer::class, ['use' => 'echo'])->withConfiguredRule(NoSpacesAroundOffsetFixer::class, ['positions' => ['inside', 'outside']])->withConfiguredRule(NoSuperfluousPhpdocTagsFixer::class, ['allow_mixed' => \true, 'allow_unused_params' => \true])->withConfiguredRule(NoUnneededControlParenthesesFixer::class, ['statements' => ['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield']])->withConfiguredRule(OrderedImportsFixer::class, ['sort_algorithm' => 'alpha'])->withConfiguredRule(PhpdocOrderFixer::class, ['order' => ['param', 'return', 'throws']])->withConfiguredRule(PhpdocSeparationFixer::class, ['groups' => [['deprecated', 'link', 'see', 'since'], ['author', 'copyright', 'license'], ['category', 'package', 'subpackage'], ['property', 'property-read', 'property-write'], ['param', 'return']]])->withConfiguredRule(PhpdocTagTypeFixer::class, ['tags' => ['inheritdoc' => 'inline']])->withConfiguredRule(ReturnTypeDeclarationFixer::class, ['space_before' => 'none'])->withConfiguredRule(SingleClassElementPerStatementFixer::class, ['elements' => ['const', 'property']])->withConfiguredRule(SingleLineCommentStyleFixer::class, ['comment_types' => ['hash']])->withConfiguredRule(TrailingCommaInMultilineFixer::class, ['elements' => ['arrays']])->withConfiguredRule(VisibilityRequiredFixer::class, ['elements' => ['method', 'property']])->withSkip([
    PhpdocSummaryFixer::class,
    PhpdocToCommentFixer::class,
    PsrAutoloadingFixer::class,
    SelfAccessorFixer::class,
    SimplifiedNullReturnFixer::class,
    StatementIndentationFixer::class,
    // App\Factories\ConfigurationFactory::$notName
    '_ide_helper*.php',
    '.phpstorm.meta.php',
    '*.blade.php',
    // App\Factories\ConfigurationFactory::$exclude
    __DIR__ . 'bootstrap/cache',
    __DIR__ . 'build',
    __DIR__ . 'node_modules',
    __DIR__ . 'storage',
]);
