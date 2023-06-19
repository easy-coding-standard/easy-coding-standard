<?php

declare (strict_types=1);
namespace ECSPrefix202306;

use ECSPrefix202306\SebastianBergmann\Diff\Differ;
use ECSPrefix202306\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix202306\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use ECSPrefix202306\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use ECSPrefix202306\Symplify\PackageBuilder\Diff\DifferFactory;
use ECSPrefix202306\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use function ECSPrefix202306\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->set(ColorConsoleDiffFormatter::class);
    $services->set(ConsoleDiffer::class);
    $services->set(DifferFactory::class);
    $services->set(Differ::class)->factory([service(DifferFactory::class), 'create']);
    $services->set(PrivatesAccessor::class);
};
