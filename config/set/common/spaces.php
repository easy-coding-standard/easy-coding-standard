<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer;
use PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer;
use SlevomatCodingStandard\Sniffs\Classes\ParentCallSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\TraitUseSpacingSniff;
use SlevomatCodingStandard\Sniffs\Whitespaces\DuplicateSpacesSniff;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ParentCallSpacingSniff::class);

    $services->set(DuplicateSpacesSniff::class);

    $services->set(MethodChainingIndentationFixer::class);

    $services->set(ClassAttributesSeparationFixer::class)
        ->call('configure', [['elements' => ['const', 'property', 'method']]]);

    $services->set(ConcatSpaceFixer::class)
        ->call('configure', [['spacing' => 'one']]);

    $services->set(NotOperatorWithSuccessorSpaceFixer::class);

    $services->set('PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff')
        ->property('ignoreBlankLines', false);

    $services->set(CastSpacesFixer::class);

    $services->set(BinaryOperatorSpacesFixer::class)
        ->call('configure', [['align_double_arrow' => false, 'align_equals' => false]]);

    $services->set(FunctionTypehintSpaceFixer::class);

    $services->set(NoBlankLinesAfterClassOpeningFixer::class);

    $services->set(NoSinglelineWhitespaceBeforeSemicolonsFixer::class);

    $services->set(PhpdocSingleLineVarSpacingFixer::class);

    $services->set(NoLeadingNamespaceWhitespaceFixer::class);

    $services->set(NoSpacesAroundOffsetFixer::class);

    $services->set(NoWhitespaceInBlankLineFixer::class);

    $services->set(ReturnTypeDeclarationFixer::class);

    $services->set(SpaceAfterSemicolonFixer::class);

    $services->set(TernaryOperatorSpacesFixer::class);

    $services->set(MethodArgumentSpaceFixer::class);

    $services->set(LanguageConstructSpacingSniff::class);

    $services->set(TraitUseSpacingSniff::class)
        ->property('linesCountAfterLastUse', 1)
        ->property('linesCountAfterLastUseWhenLastInClass', 0)
        ->property('linesCountBeforeFirstUse', 0)
        ->property('linesCountBetweenUses', 0);
};
