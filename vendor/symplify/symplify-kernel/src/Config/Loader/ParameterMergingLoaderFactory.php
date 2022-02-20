<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\SymplifyKernel\Config\Loader;

use ECSPrefix20220220\Symfony\Component\Config\FileLocator;
use ECSPrefix20220220\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20220220\Symfony\Component\Config\Loader\GlobFileLoader;
use ECSPrefix20220220\Symfony\Component\Config\Loader\LoaderResolver;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20220220\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
use ECSPrefix20220220\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;
final class ParameterMergingLoaderFactory implements \ECSPrefix20220220\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface
{
    public function create(\ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \ECSPrefix20220220\Symfony\Component\Config\Loader\LoaderInterface
    {
        $fileLocator = new \ECSPrefix20220220\Symfony\Component\Config\FileLocator([$currentWorkingDirectory]);
        $loaders = [new \ECSPrefix20220220\Symfony\Component\Config\Loader\GlobFileLoader($fileLocator), new \ECSPrefix20220220\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader($containerBuilder, $fileLocator)];
        $loaderResolver = new \ECSPrefix20220220\Symfony\Component\Config\Loader\LoaderResolver($loaders);
        return new \ECSPrefix20220220\Symfony\Component\Config\Loader\DelegatingLoader($loaderResolver);
    }
}
