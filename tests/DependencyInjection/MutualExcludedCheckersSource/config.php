<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(IndentationTypeFixer::class);
    $services->set(DisallowTabIndentSniff::class);

    // See https://github.com/symplify/symplify/issues/1702
    $services->set(IncludeFixer::class);
    $services->set(LanguageConstructSpacingSniff::class);
};
