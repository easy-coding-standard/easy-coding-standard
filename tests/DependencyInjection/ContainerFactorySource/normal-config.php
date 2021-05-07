<?php

declare (strict_types=1);
namespace ECSPrefix20210507;

use ECSPrefix20210507\SlevomatCodingStandard\Sniffs\Files\LineLengthSniff;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(LineLengthSniff::class);
};
