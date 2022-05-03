<?php

declare (strict_types=1);
namespace ECSPrefix20220503;

use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;
use PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\LanguageConstruct\ExplicitIndirectVariableFixer;
use PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer;
use PhpCsFixer\Fixer\Operator\NewWithBracesFixer;
use PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig) : void {
    $ecsConfig->rule(\PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\LanguageConstruct\ExplicitIndirectVariableFixer::class);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer::class, ['elements' => ['const', 'property']]);
    $ecsConfig->rule(\PhpCsFixer\Fixer\Operator\NewWithBracesFixer::class);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer::class, ['single_line' => \true]);
    $ecsConfig->rule(\PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer::class);
    $ecsConfig->rule(\PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer::class);
    $ecsConfig->rule(\PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer::class);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer::class, ['equal' => \false, 'identical' => \false, 'less_and_greater' => \false]);
    $ecsConfig->rule(\PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class);
    $ecsConfig->skip([\PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff::class . '.FoundInWhileCondition']);
};
