<?php

declare (strict_types=1);
namespace ECSPrefix20220220;

use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(\PhpCsFixer\Fixer\Strict\StrictComparisonFixer::class);
    $services->set(\PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer::class);
    $services->set(\PhpCsFixer\Fixer\Strict\StrictParamFixer::class);
    $services->set(\PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class);
};
