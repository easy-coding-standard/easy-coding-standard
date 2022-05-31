<?php

declare (strict_types=1);
namespace ECSPrefix20220531;

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
    $ecsConfig->rules([\PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer::class, \PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer::class, \PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer::class, \PhpCsFixer\Fixer\LanguageConstruct\ExplicitIndirectVariableFixer::class, \PhpCsFixer\Fixer\Operator\NewWithBracesFixer::class, \PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer::class, \PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer::class, \PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer::class, \PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff::class, \PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer::class, \PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer::class, \PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer::class, ['elements' => ['const', 'property']]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer::class, ['single_line' => \true]);
    $ecsConfig->ruleWithConfiguration(\PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer::class, ['equal' => \false, 'identical' => \false, 'less_and_greater' => \false]);
};
