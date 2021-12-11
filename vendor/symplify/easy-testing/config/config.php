<?php

declare (strict_types=1);
namespace ECSPrefix20211211;

use ECSPrefix20211211\Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20211211\Symplify\EasyTesting\Command\ValidateFixtureSkipNamingCommand;
use function ECSPrefix20211211\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20211211\Symplify\EasyTesting\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/DataProvider', __DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    // console
    $services->set(\ECSPrefix20211211\Symfony\Component\Console\Application::class)->call('add', [\ECSPrefix20211211\Symfony\Component\DependencyInjection\Loader\Configurator\service(\ECSPrefix20211211\Symplify\EasyTesting\Command\ValidateFixtureSkipNamingCommand::class)]);
};
