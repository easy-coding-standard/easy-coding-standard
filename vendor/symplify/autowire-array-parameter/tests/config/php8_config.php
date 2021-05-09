<?php

declare (strict_types=1);
namespace ECSPrefix20210509;

use ECSPrefix20210509\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\ECSPrefix20210509\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->autowire()->autoconfigure()->public();
    $services->load('Symplify\\AutowireArrayParameter\\Tests\\SourcePhp8\\', __DIR__ . '/../SourcePhp8');
};
