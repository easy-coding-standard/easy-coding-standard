<?php

declare (strict_types=1);
namespace ECSPrefix202408;

use ECSPrefix202408\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('Symplify\\EasyParallel\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/ValueObject', __DIR__ . '/../src/Enum', __DIR__ . '/../src/Exception', __DIR__ . '/../src/Contract']);
};
