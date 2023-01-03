<?php

declare (strict_types=1);
namespace ECSPrefix202301\Symplify\SymplifyKernel\Config\Loader;

use ECSPrefix202301\Symfony\Component\Config\FileLocator;
use ECSPrefix202301\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix202301\Symfony\Component\Config\Loader\GlobFileLoader;
use ECSPrefix202301\Symfony\Component\Config\Loader\LoaderResolver;
use ECSPrefix202301\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix202301\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
use ECSPrefix202301\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;
final class ParameterMergingLoaderFactory implements LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \ECSPrefix202301\Symfony\Component\Config\Loader\LoaderInterface
    {
        $fileLocator = new FileLocator([$currentWorkingDirectory]);
        $loaders = [new GlobFileLoader($fileLocator), new ParameterMergingPhpFileLoader($containerBuilder, $fileLocator)];
        $loaderResolver = new LoaderResolver($loaders);
        return new DelegatingLoader($loaderResolver);
    }
}
