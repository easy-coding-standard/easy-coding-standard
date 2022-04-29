<?php

declare (strict_types=1);
namespace ECSPrefix20220429;

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
    $ecsConfig->rule(\Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer::class);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer::class, ['syntax' => 'short']);
    $ecsConfig->rule(\PhpCsFixer\Fixer\Import\NoUnusedImportsFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\Import\OrderedImportsFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer::class);
};
