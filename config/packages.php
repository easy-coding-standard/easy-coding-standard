<?php

namespace ECSPrefix20210513;

use ECSPrefix20210513\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\ECSPrefix20210513\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\EasyCodingStandard\\', __DIR__ . '/../packages')->exclude(['*/Exception/*', '*/ValueObject/*', __DIR__ . '/../packages/SniffRunner/ValueObject/File.php']);
};
