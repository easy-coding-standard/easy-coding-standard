<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff;
use PhpCsFixer\Fixer\Semicolon\SemicolonAfterInstructionFixer;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ClosingTagSniff::class);
    $services->set(SemicolonAfterInstructionFixer::class);
    $services->set(UnusedVariableSniff::class);
};
