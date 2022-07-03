<?php

declare (strict_types=1);
namespace ECSPrefix202207;

use ECSPrefix202207\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix202207\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix202207\Symplify\ComposerJsonManipulator\ValueObject\Option;
use ECSPrefix202207\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use ECSPrefix202207\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix202207\Symplify\PackageBuilder\Reflection\PrivatesCaller;
use ECSPrefix202207\Symplify\SmartFileSystem\SmartFileSystem;
use function ECSPrefix202207\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::INLINE_SECTIONS, ['keywords']);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('ECSPrefix202207\Symplify\ComposerJsonManipulator\\', __DIR__ . '/../src');
    $services->set(SmartFileSystem::class);
    $services->set(PrivatesCaller::class);
    $services->set(ParameterProvider::class)->args([service('service_container')]);
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);
};
