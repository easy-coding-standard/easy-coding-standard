<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;
use PhpCsFixer\Fixer\Basic\Psr4Fixer;
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
use SlevomatCodingStandard\Sniffs\Classes\DisallowMultiConstantDefinitionSniff;
use SlevomatCodingStandard\Sniffs\Classes\DisallowMultiPropertyDefinitionSniff;
use SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff;
use SlevomatCodingStandard\Sniffs\Classes\TraitUseDeclarationSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(DisallowMultiConstantDefinitionSniff::class);

    $services->set(DisallowMultiPropertyDefinitionSniff::class);

    $services->set(PhpUnitMethodCasingFixer::class);

    $services->set(ModernClassNameReferenceSniff::class);

    $services->set(FunctionToConstantFixer::class);

    $services->set(ExplicitStringVariableFixer::class);

    $services->set(ExplicitIndirectVariableFixer::class);

    $services->set(Psr4Fixer::class);

    $services->set(SingleClassElementPerStatementFixer::class)
        ->call('configure', [[
            'elements' => ['const', 'property'],
        ]]);

    $services->set(NewWithBracesFixer::class);

    $services->set(ClassDefinitionFixer::class)
        ->call('configure', [[
            'singleLine' => true,
        ]]);

    $services->set(StandardizeIncrementFixer::class);

    $services->set(SelfAccessorFixer::class);

    $services->set(MagicConstantCasingFixer::class);

    $services->set(AssignmentInConditionSniff::class);

    $services->set(NoUselessElseFixer::class);

    $services->set(SingleQuoteFixer::class);

    $services->set(YodaStyleFixer::class)
        ->call('configure', [[
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ]]);

    $services->set(OrderedClassElementsFixer::class);

    $services->set(TraitUseDeclarationSniff::class);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        AssignmentInConditionSniff::class . '.FoundInWhileCondition' => null,
    ]);
};
