<?php

declare (strict_types=1);
namespace ECSPrefix20210507;

use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use ECSPrefix20210507\SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(DisallowYodaComparisonSniff::class);
    $services->set(YodaStyleFixer::class);
};
