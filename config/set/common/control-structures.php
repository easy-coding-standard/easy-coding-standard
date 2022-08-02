<?php

declare (strict_types=1);
namespace ECSPrefix202208;

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
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->rules([PhpUnitMethodCasingFixer::class, FunctionToConstantFixer::class, ExplicitStringVariableFixer::class, ExplicitIndirectVariableFixer::class, NewWithBracesFixer::class, StandardizeIncrementFixer::class, SelfAccessorFixer::class, MagicConstantCasingFixer::class, AssignmentInConditionSniff::class, NoUselessElseFixer::class, SingleQuoteFixer::class, OrderedClassElementsFixer::class]);
    $ecsConfig->ruleWithConfiguration(SingleClassElementPerStatementFixer::class, ['elements' => ['const', 'property']]);
    $ecsConfig->ruleWithConfiguration(ClassDefinitionFixer::class, ['single_line' => \true]);
    $ecsConfig->ruleWithConfiguration(YodaStyleFixer::class, ['equal' => \false, 'identical' => \false, 'less_and_greater' => \false]);
};
