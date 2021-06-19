<?php

declare (strict_types=1);
namespace ECSPrefix20210619;

use ECSPrefix20210619\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20210619\Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20210619\Symplify\ComposerJsonManipulator\ValueObject\Option;
use ECSPrefix20210619\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use ECSPrefix20210619\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20210619\Symplify\PackageBuilder\Reflection\PrivatesCaller;
use ECSPrefix20210619\Symplify\SmartFileSystem\SmartFileSystem;
use function ECSPrefix20210619\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(\ECSPrefix20210619\Symplify\ComposerJsonManipulator\ValueObject\Option::INLINE_SECTIONS, ['keywords']);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20210619\Symplify\ComposerJsonManipulator\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Bundle']);
    $services->set(\ECSPrefix20210619\Symplify\SmartFileSystem\SmartFileSystem::class);
    $services->set(\ECSPrefix20210619\Symplify\PackageBuilder\Reflection\PrivatesCaller::class);
    $services->set(\ECSPrefix20210619\Symplify\PackageBuilder\Parameter\ParameterProvider::class)->args([\ECSPrefix20210619\Symfony\Component\DependencyInjection\Loader\Configurator\service(\ECSPrefix20210619\Symfony\Component\DependencyInjection\ContainerInterface::class)]);
    $services->set(\ECSPrefix20210619\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class);
    $services->set(\ECSPrefix20210619\Symfony\Component\Console\Style\SymfonyStyle::class)->factory([\ECSPrefix20210619\Symfony\Component\DependencyInjection\Loader\Configurator\service(\ECSPrefix20210619\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class), 'create']);
};
