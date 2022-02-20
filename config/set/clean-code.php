<?php

declare (strict_types=1);
namespace ECSPrefix20220220;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(\Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer::class);
    $services->set(\PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer::class)->call('configure', [['syntax' => 'short']]);
    $services->set(\PhpCsFixer\Fixer\Import\NoUnusedImportsFixer::class);
    $services->set(\PhpCsFixer\Fixer\Import\OrderedImportsFixer::class);
    $services->set(\PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer::class);
    $services->set(\PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer::class);
    $services->set(\PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer::class);
    $services->set(\PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer::class);
};
