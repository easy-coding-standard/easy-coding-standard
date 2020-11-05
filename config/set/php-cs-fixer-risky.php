<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Alias\EregToPregFixer;
use PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer;
use PhpCsFixer\Fixer\Alias\SetTypeToCastFixer;
use PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer;
use PhpCsFixer\Fixer\Basic\Psr4Fixer;
use PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer;
use PhpCsFixer\Fixer\ClassNotation\FinalInternalClassFixer;
use PhpCsFixer\Fixer\ClassNotation\NoUnneededFinalMethodFixer;
use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\Comment\CommentToPhpdocFixer;
use PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FopenFlagOrderFixer;
use PhpCsFixer\Fixer\FunctionNotation\FopenFlagsFixer;
use PhpCsFixer\Fixer\FunctionNotation\ImplodeCallFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoUnreachableDefaultArgumentValueFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DirConstantFixer;
use PhpCsFixer\Fixer\LanguageConstruct\ErrorSuppressionFixer;
use PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer;
use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\LanguageConstruct\NoUnsetOnPropertyFixer;
use PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer;
use PhpCsFixer\Fixer\Operator\LogicalOperatorsFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMockShortWillReturnFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use PhpCsFixer\Fixer\StringNotation\StringLineEndingFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(CommentToPhpdocFixer::class);
    $services->set(DirConstantFixer::class);
    $services->set(EregToPregFixer::class);
    $services->set(ErrorSuppressionFixer::class);
    $services->set(FinalInternalClassFixer::class);
    $services->set(FopenFlagOrderFixer::class);
    $services->set(FopenFlagsFixer::class)
        ->call('configure', [[
            'b_mode' => false,
        ]]);
    $services->set(FunctionToConstantFixer::class)
        ->call('configure', [[
            'functions' => ['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi'],
        ]]);
    $services->set(ImplodeCallFixer::class);
    $services->set(IsNullFixer::class);
    $services->set(LogicalOperatorsFixer::class);
    $services->set(ModernizeTypesCastingFixer::class);
    $services->set(NativeConstantInvocationFixer::class)
        ->call('configure', [[
            'fix_built_in' => false,
            'include' => ['DIRECTORY_SEPARATOR', 'PHP_SAPI', 'PHP_VERSION_ID'],
            'scope' => 'namespaced',
        ]]);
    $services->set(NativeFunctionInvocationFixer::class)
        ->call('configure', [[
            'include' => ['@compiler_optimized'],
            'scope' => 'namespaced',
            'strict' => true,
        ]]);
    $services->set(NoAliasFunctionsFixer::class);
    $services->set(NoHomoglyphNamesFixer::class);
    $services->set(NoUnneededFinalMethodFixer::class);
    $services->set(NoUnreachableDefaultArgumentValueFixer::class);
    $services->set(NoUnsetOnPropertyFixer::class);
    $services->set(NonPrintableCharacterFixer::class);
    $services->set(PhpUnitConstructFixer::class);
    $services->set(PhpUnitMockShortWillReturnFixer::class);
    $services->set(PhpUnitSetUpTearDownVisibilityFixer::class);
    $services->set(PhpUnitStrictFixer::class);
    $services->set(PhpUnitTestAnnotationFixer::class);
    $services->set(PhpUnitTestCaseStaticMethodCallsFixer::class);
    $services->set(Psr4Fixer::class);
    $services->set(SelfAccessorFixer::class);
    $services->set(SetTypeToCastFixer::class);
    $services->set(StrictComparisonFixer::class);
    $services->set(StrictParamFixer::class);
    $services->set(StringLineEndingFixer::class);
};
