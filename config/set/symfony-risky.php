<?php

declare (strict_types=1);
namespace ECSPrefix202206;

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
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->ruleWithConfiguration(FopenFlagsFixer::class, ['b_mode' => \false]);
    $ecsConfig->ruleWithConfiguration(FunctionToConstantFixer::class, ['functions' => ['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi']]);
    $ecsConfig->ruleWithConfiguration(NativeConstantInvocationFixer::class, ['fix_built_in' => \false, 'include' => ['DIRECTORY_SEPARATOR', 'PHP_SAPI', 'PHP_VERSION_ID'], 'scope' => 'namespaced']);
    $ecsConfig->ruleWithConfiguration(NativeFunctionInvocationFixer::class, ['include' => [NativeFunctionInvocationFixer::SET_COMPILER_OPTIMIZED], 'scope' => 'namespaced', 'strict' => \true]);
    $ecsConfig->rules([ImplodeCallFixer::class, IsNullFixer::class, ModernizeTypesCastingFixer::class, NoAliasFunctionsFixer::class, NoHomoglyphNamesFixer::class, NoUnneededFinalMethodFixer::class, NonPrintableCharacterFixer::class, PhpUnitConstructFixer::class, PhpUnitMockShortWillReturnFixer::class, SelfAccessorFixer::class, SetTypeToCastFixer::class, DirConstantFixer::class, EregToPregFixer::class, ErrorSuppressionFixer::class, FopenFlagOrderFixer::class]);
};
