<?php

declare (strict_types=1);
namespace ECSPrefix20211102;

use ECSPrefix20211102\SebastianBergmann\Diff\Differ;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20211102\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20211102\Symplify\ConsoleColorDiff\\', __DIR__ . '/../src');
    $services->set(\ECSPrefix20211102\SebastianBergmann\Diff\Differ::class);
    $services->set(\ECSPrefix20211102\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
