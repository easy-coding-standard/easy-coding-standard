<?php

declare (strict_types=1);
namespace ECSPrefix20211031\Symplify\SymfonyContainerBuilder\Config\Loader;

use ECSPrefix20211031\Symfony\Component\Config\FileLocator;
use ECSPrefix20211031\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20211031\Symfony\Component\Config\Loader\GlobFileLoader;
use ECSPrefix20211031\Symfony\Component\Config\Loader\LoaderResolver;
use ECSPrefix20211031\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20211031\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
final class ParameterMergingLoaderFactory
{
    public function create(\ECSPrefix20211031\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \ECSPrefix20211031\Symfony\Component\Config\Loader\DelegatingLoader
    {
        $fileLocator = new \ECSPrefix20211031\Symfony\Component\Config\FileLocator([$currentWorkingDirectory]);
        $loaders = [new \ECSPrefix20211031\Symfony\Component\Config\Loader\GlobFileLoader($fileLocator), new \ECSPrefix20211031\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader($containerBuilder, $fileLocator)];
        $loaderResolver = new \ECSPrefix20211031\Symfony\Component\Config\Loader\LoaderResolver($loaders);
        return new \ECSPrefix20211031\Symfony\Component\Config\Loader\DelegatingLoader($loaderResolver);
    }
}
