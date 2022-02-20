<?php

declare (strict_types=1);
namespace ECSPrefix20220220;

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
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(\PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer::class);
    $services->set(\PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer::class);
    $services->set(\PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer::class);
    $services->set(\PhpCsFixer\Fixer\LanguageConstruct\ExplicitIndirectVariableFixer::class);
    $services->set(\PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer::class)->call('configure', [['elements' => ['const', 'property']]]);
    $services->set(\PhpCsFixer\Fixer\Operator\NewWithBracesFixer::class);
    $services->set(\PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer::class)->call('configure', [['single_line' => \true]]);
    $services->set(\PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer::class);
    $services->set(\PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer::class);
    $services->set(\PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer::class);
    $services->set(\PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff::class);
    $services->set(\PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer::class);
    $services->set(\PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer::class);
    $services->set(\PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer::class)->call('configure', [['equal' => \false, 'identical' => \false, 'less_and_greater' => \false]]);
    $services->set(\PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class);
    $parameters = $containerConfigurator->parameters();
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::SKIP, [\PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff::class . '.FoundInWhileCondition']);
};
