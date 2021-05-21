<?php

declare (strict_types=1);
namespace ECSPrefix20210521;

use ECSPrefix20210521\Symfony\Component\Console\Application;
use ECSPrefix20210521\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20210521\Symplify\EasyTesting\Console\EasyTestingConsoleApplication;
use ECSPrefix20210521\Symplify\PackageBuilder\Console\Command\CommandNaming;
return static function (\ECSPrefix20210521\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20210521\Symplify\EasyTesting\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/DataProvider', __DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/ValueObject']);
    // console
    $services->set(\ECSPrefix20210521\Symplify\EasyTesting\Console\EasyTestingConsoleApplication::class);
    $services->alias(\ECSPrefix20210521\Symfony\Component\Console\Application::class, \ECSPrefix20210521\Symplify\EasyTesting\Console\EasyTestingConsoleApplication::class);
    $services->set(\ECSPrefix20210521\Symplify\PackageBuilder\Console\Command\CommandNaming::class);
};
