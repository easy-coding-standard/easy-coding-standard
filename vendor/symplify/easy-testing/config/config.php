<?php

namespace ECSPrefix20210511;

use ECSPrefix20210511\Symfony\Component\Console\Application;
use ECSPrefix20210511\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyTesting\Console\EasyTestingConsoleApplication;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
return static function (\ECSPrefix20210511\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\EasyTesting\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/DataProvider', __DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/ValueObject']);
    // console
    $services->set(\Symplify\EasyTesting\Console\EasyTestingConsoleApplication::class);
    $services->alias(\ECSPrefix20210511\Symfony\Component\Console\Application::class, \Symplify\EasyTesting\Console\EasyTestingConsoleApplication::class);
    $services->set(\Symplify\PackageBuilder\Console\Command\CommandNaming::class);
};
