<?php

declare(strict_types=1);

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
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(EncodingFixer::class);

    $services->set(FullOpeningTagFixer::class);

    $services->set(BlankLineAfterNamespaceFixer::class);

    $services->set(BracesFixer::class);

    $services->set(ClassDefinitionFixer::class);

    $services->set(ConstantCaseFixer::class);

    $services->set(ElseifFixer::class);

    $services->set(FunctionDeclarationFixer::class);

    $services->set(IndentationTypeFixer::class);

    $services->set(LineEndingFixer::class);

    $services->set(LowercaseKeywordsFixer::class);

    $services->set(MethodArgumentSpaceFixer::class)
        ->call('configure', [[
            'ensure_fully_multiline' => true,
        ]]);

    $services->set(NoBreakCommentFixer::class);

    $services->set(NoClosingTagFixer::class);

    $services->set(NoSpacesAfterFunctionNameFixer::class);

    $services->set(NoSpacesInsideParenthesisFixer::class);

    $services->set(NoTrailingWhitespaceFixer::class);

    $services->set(NoTrailingWhitespaceInCommentFixer::class);

    $services->set(SingleBlankLineAtEofFixer::class);

    $services->set(SingleClassElementPerStatementFixer::class)
        ->call('configure', [[
            'elements' => ['property'],
        ]]);

    $services->set(SingleImportPerStatementFixer::class);

    $services->set(SingleLineAfterImportsFixer::class);

    $services->set(SwitchCaseSemicolonToColonFixer::class);

    $services->set(SwitchCaseSpaceFixer::class);

    $services->set(VisibilityRequiredFixer::class);
};
