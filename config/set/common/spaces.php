<?php

declare (strict_types=1);
namespace ECSPrefix202208;

use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleTraitInsertPerStatementFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer;
use PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer;
use Symplify\CodingStandard\Fixer\Spacing\NewlineServiceDefinitionConfigFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->rules([StandaloneLinePromotedPropertyFixer::class, BlankLineAfterOpeningTagFixer::class, NewlineServiceDefinitionConfigFixer::class, MethodChainingIndentationFixer::class, NotOperatorWithSuccessorSpaceFixer::class, CastSpacesFixer::class, ClassAttributesSeparationFixer::class, SingleTraitInsertPerStatementFixer::class, FunctionTypehintSpaceFixer::class, NoBlankLinesAfterClassOpeningFixer::class, NoSinglelineWhitespaceBeforeSemicolonsFixer::class, PhpdocSingleLineVarSpacingFixer::class, NoLeadingNamespaceWhitespaceFixer::class, NoSpacesAroundOffsetFixer::class, NoWhitespaceInBlankLineFixer::class, ReturnTypeDeclarationFixer::class, SpaceAfterSemicolonFixer::class, TernaryOperatorSpacesFixer::class, MethodArgumentSpaceFixer::class, LanguageConstructSpacingSniff::class]);
    $ecsConfig->ruleWithConfiguration(ClassAttributesSeparationFixer::class, ['elements' => ['const' => 'one', 'property' => 'one', 'method' => 'one']]);
    $ecsConfig->ruleWithConfiguration(ConcatSpaceFixer::class, ['spacing' => 'one']);
    $ecsConfig->ruleWithConfiguration(SuperfluousWhitespaceSniff::class, ['ignoreBlankLines' => \false]);
    $ecsConfig->ruleWithConfiguration(BinaryOperatorSpacesFixer::class, ['operators' => ['=>' => 'single_space', '=' => 'single_space']]);
};
