<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\ControlStructures\InlineControlStructureSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\DisallowMultipleStatementsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\FunctionCallArgumentSpacingSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Functions\ValidDefaultValueSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\ControlStructureSpacingSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\ElseIfDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionCallSignatureSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionClosingBraceSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\MethodDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\NamespaceDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\UseDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\ControlSignatureSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\ForEachLoopDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\ForLoopDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\LowercaseDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationArgumentSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\LowercaseFunctionKeywordsSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\MultiLineFunctionDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Scope\MethodScopeSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ScopeClosingBraceSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ScopeKeywordSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(NamespaceDeclarationSniff::class);

    $services->set(UseDeclarationSniff::class);

    $services->set(ClassDeclarationSniff::class);

    $services->set(PropertyDeclarationSniff::class);

    $services->set(EndFileNewlineSniff::class);

    $services->set(ClosingTagSniff::class);

    $services->set(ControlStructureSpacingSniff::class);

    $services->set(SwitchDeclarationSniff::class);

    $services->set(ElseIfDeclarationSniff::class);

    $services->set(FunctionCallSignatureSniff::class);

    $services->set(MethodDeclarationSniff::class);

    $services->set(FunctionClosingBraceSniff::class);

    $services->set(ByteOrderMarkSniff::class);

    $services->set(ValidClassNameSniff::class);

    $services->set(UpperCaseConstantNameSniff::class);

    $services->set(LineEndingsSniff::class)
        ->property('eolChar', '\n');

    $services->set(SuperfluousWhitespaceSniff::class)
        ->property('ignoreBlankLines', true);

    $services->set(DisallowMultipleStatementsSniff::class);

    $services->set(ScopeIndentSniff::class)
        ->property('ignoreIndentationTokens', ['T_COMMENT', 'T_DOC_COMMENT_OPEN_TAG']);

    $services->set(DisallowTabIndentSniff::class);

    $services->set(LowerCaseKeywordSniff::class);

    $services->set(LowerCaseConstantSniff::class);

    $services->set(MethodScopeSniff::class);

    $services->set(ScopeKeywordSpacingSniff::class);

    $services->set(FunctionDeclarationSniff::class);

    $services->set(LowercaseFunctionKeywordsSniff::class);

    $services->set(FunctionDeclarationArgumentSpacingSniff::class)
        ->property('equalsSpacing', 1);

    $services->set(ValidDefaultValueSniff::class);

    $services->set(MultiLineFunctionDeclarationSniff::class);

    $services->set(FunctionCallArgumentSpacingSniff::class);

    $services->set(ControlSignatureSniff::class);

    $services->set(ScopeClosingBraceSniff::class);

    $services->set(ForEachLoopDeclarationSniff::class);

    $services->set(ForLoopDeclarationSniff::class);

    $services->set(LowercaseDeclarationSniff::class);

    $services->set(InlineControlStructureSniff::class);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        ControlStructureSpacingSniff::class . '.SpacingAfterOpenBrace' => null,
        ControlStructureSpacingSniff::class . '.SpaceBeforeCloseBrace' => null,
        ControlStructureSpacingSniff::class . '.LineAfterClose' => null,
        ControlStructureSpacingSniff::class . '.NoLineAfterClose' => null,
        FunctionCallSignatureSniff::class . '.OpeningIndent' => null,
    ]);
};
