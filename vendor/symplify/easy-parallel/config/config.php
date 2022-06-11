<?php

declare (strict_types=1);
namespace ECSPrefix20220611;

use ECSPrefix20220611\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('ECSPrefix20220611\Symplify\EasyParallel\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/ValueObject']);
};
