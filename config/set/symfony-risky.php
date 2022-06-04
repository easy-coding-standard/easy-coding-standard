<?php

declare (strict_types=1);
namespace ECSPrefix20220604;

use PhpCsFixer\Fixer\Alias\EregToPregFixer;
use PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer;
use PhpCsFixer\Fixer\Alias\SetTypeToCastFixer;
use PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer;
use PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer;
use PhpCsFixer\Fixer\ClassNotation\NoUnneededFinalMethodFixer;
use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FopenFlagOrderFixer;
use PhpCsFixer\Fixer\FunctionNotation\FopenFlagsFixer;
use PhpCsFixer\Fixer\FunctionNotation\ImplodeCallFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DirConstantFixer;
use PhpCsFixer\Fixer\LanguageConstruct\ErrorSuppressionFixer;
use PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer;
use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMockShortWillReturnFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\FunctionNotation\FopenFlagsFixer::class, ['b_mode' => \false]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer::class, ['functions' => ['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi']]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer::class, ['fix_built_in' => \false, 'include' => ['DIRECTORY_SEPARATOR', 'PHP_SAPI', 'PHP_VERSION_ID'], 'scope' => 'namespaced']);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer::class, ['include' => [\PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer::SET_COMPILER_OPTIMIZED], 'scope' => 'namespaced', 'strict' => \true]);
    $ecsConfig->rules([\PhpCsFixer\Fixer\FunctionNotation\ImplodeCallFixer::class, \PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer::class, \PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer::class, \PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer::class, \PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer::class, \PhpCsFixer\Fixer\ClassNotation\NoUnneededFinalMethodFixer::class, \PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer::class, \PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer::class, \PhpCsFixer\Fixer\PhpUnit\PhpUnitMockShortWillReturnFixer::class, \PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer::class, \PhpCsFixer\Fixer\Alias\SetTypeToCastFixer::class, \PhpCsFixer\Fixer\LanguageConstruct\DirConstantFixer::class, \PhpCsFixer\Fixer\Alias\EregToPregFixer::class, \PhpCsFixer\Fixer\LanguageConstruct\ErrorSuppressionFixer::class, \PhpCsFixer\Fixer\FunctionNotation\FopenFlagOrderFixer::class]);
};
