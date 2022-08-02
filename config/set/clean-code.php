<?php

declare (strict_types=1);
namespace ECSPrefix202208;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, ['syntax' => 'short']);
    $ecsConfig->rules([ParamReturnAndVarTagMalformsFixer::class, NoUnusedImportsFixer::class, OrderedImportsFixer::class, NoEmptyStatementFixer::class, ProtectedToPrivateFixer::class, NoUnneededControlParenthesesFixer::class, NoUnneededCurlyBracesFixer::class]);
};
