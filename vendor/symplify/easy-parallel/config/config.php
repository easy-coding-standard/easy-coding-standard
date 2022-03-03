<?php

declare (strict_types=1);
namespace ECSPrefix20220303;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20220303\Symplify\EasyParallel\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/ValueObject']);
};
