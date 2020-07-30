<?php declare(strict_types=1);

use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(StrictComparisonFixer::class);

    $services->set(IsNullFixer::class)
        ->call('configure', [['use_yoda_style' => false]]);

    $services->set(StrictParamFixer::class);
};
