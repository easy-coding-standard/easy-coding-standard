<?php

declare (strict_types=1);
namespace ECSPrefix20220606;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer::class, ['syntax' => 'short']);
    $ecsConfig->rules([\Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer::class, \PhpCsFixer\Fixer\Import\NoUnusedImportsFixer::class, \PhpCsFixer\Fixer\Import\OrderedImportsFixer::class, \PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer::class, \PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer::class, \PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer::class, \PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer::class]);
};
