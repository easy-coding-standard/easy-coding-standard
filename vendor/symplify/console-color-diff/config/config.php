<?php

declare (strict_types=1);
namespace ECSPrefix20210723;

use ECSPrefix20210723\SebastianBergmann\Diff\Differ;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20210723\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20210723\Symplify\ConsoleColorDiff\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Bundle']);
    $services->set(\ECSPrefix20210723\SebastianBergmann\Diff\Differ::class);
    $services->set(\ECSPrefix20210723\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
