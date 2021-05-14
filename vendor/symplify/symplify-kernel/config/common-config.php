<?php

namespace ECSPrefix20210514;

use ECSPrefix20210514\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20210514\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20210514\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20210514\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use ECSPrefix20210514\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20210514\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use ECSPrefix20210514\Symplify\SmartFileSystem\FileSystemFilter;
use ECSPrefix20210514\Symplify\SmartFileSystem\FileSystemGuard;
use ECSPrefix20210514\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use ECSPrefix20210514\Symplify\SmartFileSystem\Finder\SmartFinder;
use ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileSystem;
use function ECSPrefix20210514\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\ECSPrefix20210514\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    // symfony style
    $services->set(\ECSPrefix20210514\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class);
    $services->set(\ECSPrefix20210514\Symfony\Component\Console\Style\SymfonyStyle::class)->factory([\ECSPrefix20210514\Symfony\Component\DependencyInjection\Loader\Configurator\service(\ECSPrefix20210514\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class), 'create']);
    // filesystem
    $services->set(\ECSPrefix20210514\Symplify\SmartFileSystem\Finder\FinderSanitizer::class);
    $services->set(\ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileSystem::class);
    $services->set(\ECSPrefix20210514\Symplify\SmartFileSystem\Finder\SmartFinder::class);
    $services->set(\ECSPrefix20210514\Symplify\SmartFileSystem\FileSystemGuard::class);
    $services->set(\ECSPrefix20210514\Symplify\SmartFileSystem\FileSystemFilter::class);
    $services->set(\ECSPrefix20210514\Symplify\PackageBuilder\Parameter\ParameterProvider::class)->args([\ECSPrefix20210514\Symfony\Component\DependencyInjection\Loader\Configurator\service(\ECSPrefix20210514\Symfony\Component\DependencyInjection\ContainerInterface::class)]);
    $services->set(\ECSPrefix20210514\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
