<?php

namespace ECSPrefix20210515;

use ECSPrefix20210515\SebastianBergmann\Diff\Differ;
use ECSPrefix20210515\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20210515\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\ECSPrefix20210515\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20210515\Symplify\ConsoleColorDiff\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Bundle']);
    $services->set(\ECSPrefix20210515\SebastianBergmann\Diff\Differ::class);
    $services->set(\ECSPrefix20210515\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
