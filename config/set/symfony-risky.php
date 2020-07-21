<?php declare(strict_types=1);

use PhpCsFixer\Fixer\Alias\EregToPregFixer;
use PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer;
use PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer;
use PhpCsFixer\Fixer\Basic\Psr4Fixer;
use PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer;
use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DirConstantFixer;
use PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer;
use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\LanguageConstruct\SilencedDeprecationErrorFixer;
use PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(DirConstantFixer::class);

    $services->set(EregToPregFixer::class);

    $services->set(FunctionToConstantFixer::class);

    $services->set(IsNullFixer::class);

    $services->set(ModernizeTypesCastingFixer::class);

    $services->set(NoAliasFunctionsFixer::class);

    $services->set(NonPrintableCharacterFixer::class)
        ->call('configure', [['use_escape_sequences_in_strings' => false]]);

    $services->set(PhpUnitConstructFixer::class);

    $services->set(PhpUnitDedicateAssertFixer::class);

    $services->set(Psr4Fixer::class);

    $services->set(SelfAccessorFixer::class);

    $services->set(SilencedDeprecationErrorFixer::class);

    $services->set(NoHomoglyphNamesFixer::class);
};
