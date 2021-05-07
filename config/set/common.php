<?php

namespace ECSPrefix20210507;

use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->import(__DIR__ . '/common/*.php');
};
