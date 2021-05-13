<?php

namespace ECSPrefix20210513;

use ECSPrefix20210513\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\ECSPrefix20210513\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->import(__DIR__ . '/common/*.php');
};
