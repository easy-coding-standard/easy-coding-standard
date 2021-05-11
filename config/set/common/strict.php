<?php

namespace ECSPrefix20210511;

use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use ECSPrefix20210511\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\ECSPrefix20210511\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->set(\PhpCsFixer\Fixer\Strict\StrictComparisonFixer::class);
    $services->set(\PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer::class);
    $services->set(\PhpCsFixer\Fixer\Strict\StrictParamFixer::class);
};
