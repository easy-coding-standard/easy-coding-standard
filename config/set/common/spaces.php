<?php

declare (strict_types=1);
namespace ECSPrefix20220604;

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
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->rules([\Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer::class, \PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer::class, \Symplify\CodingStandard\Fixer\Spacing\NewlineServiceDefinitionConfigFixer::class, \PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer::class, \PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer::class, \PhpCsFixer\Fixer\CastNotation\CastSpacesFixer::class, \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class, \PhpCsFixer\Fixer\ClassNotation\SingleTraitInsertPerStatementFixer::class, \PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer::class, \PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer::class, \PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer::class, \PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer::class, \PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer::class, \PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer::class, \PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer::class, \PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer::class, \PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer::class, \PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer::class, \PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer::class, \PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff::class]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class, ['elements' => ['const' => 'one', 'property' => 'one', 'method' => 'one']]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\Operator\ConcatSpaceFixer::class, ['spacing' => 'one']);
    $ecsConfig->ruleWithConfiguration(\PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff::class, ['ignoreBlankLines' => \false]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer::class, ['operators' => ['=>' => 'single_space', '=' => 'single_space']]);
};
