<?php

declare (strict_types=1);
namespace ConfigTransformer20210601;

use ConfigTransformer20210601\Symfony\Component\Console\Style\SymfonyStyle;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerInterface;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ConfigTransformer20210601\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use ConfigTransformer20210601\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ConfigTransformer20210601\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use ConfigTransformer20210601\Symplify\SmartFileSystem\FileSystemFilter;
use ConfigTransformer20210601\Symplify\SmartFileSystem\FileSystemGuard;
use ConfigTransformer20210601\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use ConfigTransformer20210601\Symplify\SmartFileSystem\Finder\SmartFinder;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem;
use function ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    // symfony style
    $services->set(\ConfigTransformer20210601\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class);
    $services->set(\ConfigTransformer20210601\Symfony\Component\Console\Style\SymfonyStyle::class)->factory([\ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\service(\ConfigTransformer20210601\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class), 'create']);
    // filesystem
    $services->set(\ConfigTransformer20210601\Symplify\SmartFileSystem\Finder\FinderSanitizer::class);
    $services->set(\ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem::class);
    $services->set(\ConfigTransformer20210601\Symplify\SmartFileSystem\Finder\SmartFinder::class);
    $services->set(\ConfigTransformer20210601\Symplify\SmartFileSystem\FileSystemGuard::class);
    $services->set(\ConfigTransformer20210601\Symplify\SmartFileSystem\FileSystemFilter::class);
    $services->set(\ConfigTransformer20210601\Symplify\PackageBuilder\Parameter\ParameterProvider::class)->args([\ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\service(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerInterface::class)]);
    $services->set(\ConfigTransformer20210601\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
