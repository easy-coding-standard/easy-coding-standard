<?php

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyTesting\Console\EasyTestingConsoleApplication;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\EasyTesting\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/DataProvider',
            __DIR__ . '/../src/HttpKernel',
            __DIR__ . '/../src/ValueObject',
        ]);

    // console
    $services->set(EasyTestingConsoleApplication::class);
    $services->alias(Application::class, EasyTestingConsoleApplication::class);
    $services->set(CommandNaming::class);
};
