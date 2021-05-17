<?php

declare (strict_types=1);
namespace ECSPrefix20210517;

use ECSPrefix20210517\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\ECSPrefix20210517\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->import(__DIR__ . '/common/*.php');
};
