<?php

namespace ECSPrefix20210507;

use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->autowire()->autoconfigure()->public();
    $services->load('Symplify\\EasyCodingStandard\\Configuration\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Exception']);
};
