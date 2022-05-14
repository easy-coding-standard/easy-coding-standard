<?php

declare (strict_types=1);
namespace ECSPrefix20220514;

use ECSPrefix20220514\Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20220514\Symplify\ComposerJsonManipulator\ValueObject\Option;
use ECSPrefix20220514\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use ECSPrefix20220514\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20220514\Symplify\PackageBuilder\Reflection\PrivatesCaller;
use ECSPrefix20220514\Symplify\SmartFileSystem\SmartFileSystem;
use function ECSPrefix20220514\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(\ECSPrefix20220514\Symplify\ComposerJsonManipulator\ValueObject\Option::INLINE_SECTIONS, ['keywords']);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20220514\Symplify\ComposerJsonManipulator\\', __DIR__ . '/../src');
    $services->set(\ECSPrefix20220514\Symplify\SmartFileSystem\SmartFileSystem::class);
    $services->set(\ECSPrefix20220514\Symplify\PackageBuilder\Reflection\PrivatesCaller::class);
    $services->set(\ECSPrefix20220514\Symplify\PackageBuilder\Parameter\ParameterProvider::class)->args([\ECSPrefix20220514\Symfony\Component\DependencyInjection\Loader\Configurator\service('service_container')]);
    $services->set(\ECSPrefix20220514\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class);
    $services->set(\ECSPrefix20220514\Symfony\Component\Console\Style\SymfonyStyle::class)->factory([\ECSPrefix20220514\Symfony\Component\DependencyInjection\Loader\Configurator\service(\ECSPrefix20220514\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class), 'create']);
};
