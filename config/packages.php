<?php

declare (strict_types=1);
namespace ECSPrefix20210520;

use ECSPrefix20210520\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\ECSPrefix20210520\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\EasyCodingStandard\\', __DIR__ . '/../packages')->exclude(['*/Exception/*', '*/ValueObject/*', __DIR__ . '/../packages/SniffRunner/ValueObject/File.php']);
};
