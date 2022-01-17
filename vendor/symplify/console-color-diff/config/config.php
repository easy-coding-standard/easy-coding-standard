<?php

declare (strict_types=1);
namespace ECSPrefix20220117;

use ECSPrefix20220117\SebastianBergmann\Diff\Differ;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20220117\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20220117\Symplify\ConsoleColorDiff\\', __DIR__ . '/../src');
    $services->set(\ECSPrefix20220117\SebastianBergmann\Diff\Differ::class);
    $services->set(\ECSPrefix20220117\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
