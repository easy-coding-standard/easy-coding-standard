<?php

declare (strict_types=1);
namespace ECSPrefix202208;

use ECSPrefix202208\Symfony\Component\Console\Application;
use ECSPrefix202208\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix202208\Symplify\EasyTesting\Command\ValidateFixtureSkipNamingCommand;
use function ECSPrefix202208\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('ECSPrefix202208\Symplify\EasyTesting\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/DataProvider', __DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    // console
    $services->set(Application::class)->call('add', [service(ValidateFixtureSkipNamingCommand::class)]);
};
