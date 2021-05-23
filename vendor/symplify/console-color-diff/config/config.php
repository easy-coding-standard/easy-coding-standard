<?php

declare (strict_types=1);
namespace ECSPrefix20210523;

use ECSPrefix20210523\SebastianBergmann\Diff\Differ;
use ECSPrefix20210523\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20210523\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\ECSPrefix20210523\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20210523\Symplify\ConsoleColorDiff\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Bundle']);
    $services->set(\ECSPrefix20210523\SebastianBergmann\Diff\Differ::class);
    $services->set(\ECSPrefix20210523\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
