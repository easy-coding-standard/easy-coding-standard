<?php

declare (strict_types=1);
namespace ECSPrefix20220513;

use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\ElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer;
use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\Whitespace\LineEndingFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer;
use PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->rules([\PhpCsFixer\Fixer\Basic\EncodingFixer::class, \PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer::class, \PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer::class, \PhpCsFixer\Fixer\Basic\BracesFixer::class, \PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer::class, \PhpCsFixer\Fixer\Casing\ConstantCaseFixer::class, \PhpCsFixer\Fixer\ControlStructure\ElseifFixer::class, \PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer::class, \PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer::class, \PhpCsFixer\Fixer\Whitespace\LineEndingFixer::class, \PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer::class, \PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer::class, \PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer::class, \PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer::class, \PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer::class, \PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer::class, \PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer::class, \PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer::class, \PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer::class, \PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer::class, \PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer::class, \PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer::class, \PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer::class]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer::class, ['on_multiline' => 'ensure_fully_multiline']);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer::class, ['elements' => ['property']]);
};
