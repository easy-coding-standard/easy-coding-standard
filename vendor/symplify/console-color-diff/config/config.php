<?php

declare (strict_types=1);
namespace ECSPrefix20211210;

use ECSPrefix20211210\SebastianBergmann\Diff\Differ;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20211210\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20211210\Symplify\ConsoleColorDiff\\', __DIR__ . '/../src');
    $services->set(\ECSPrefix20211210\SebastianBergmann\Diff\Differ::class);
    $services->set(\ECSPrefix20211210\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
