<?php

declare (strict_types=1);
namespace ECSPrefix20220608;

use ECSPrefix20220608\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20220608\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20220608\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use ECSPrefix20220608\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20220608\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use ECSPrefix20220608\Symplify\SmartFileSystem\FileSystemFilter;
use ECSPrefix20220608\Symplify\SmartFileSystem\FileSystemGuard;
use ECSPrefix20220608\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use ECSPrefix20220608\Symplify\SmartFileSystem\Finder\SmartFinder;
use ECSPrefix20220608\Symplify\SmartFileSystem\SmartFileSystem;
use function ECSPrefix20220608\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    // symfony style
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);
    // filesystem
    $services->set(FinderSanitizer::class);
    $services->set(SmartFileSystem::class);
    $services->set(SmartFinder::class);
    $services->set(FileSystemGuard::class);
    $services->set(FileSystemFilter::class);
    $services->set(ParameterProvider::class)->args([service('service_container')]);
    $services->set(PrivatesAccessor::class);
};
