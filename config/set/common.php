<?php

declare (strict_types=1);
namespace ECSPrefix20210605;

use ECSPrefix20210605\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\ECSPrefix20210605\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->import(__DIR__ . '/common/*.php');
};
