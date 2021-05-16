<?php

namespace ECSPrefix20210516;

use ECSPrefix20210516\SebastianBergmann\Diff\Differ;
use ECSPrefix20210516\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20210516\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\ECSPrefix20210516\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20210516\Symplify\ConsoleColorDiff\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Bundle']);
    $services->set(\ECSPrefix20210516\SebastianBergmann\Diff\Differ::class);
    $services->set(\ECSPrefix20210516\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
