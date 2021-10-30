<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20211030\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20211030\Symfony\Component\Config\Loader\GlobFileLoader;
use ECSPrefix20211030\Symfony\Component\Config\Loader\LoaderResolver;
use ECSPrefix20211030\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20211030\Symfony\Component\HttpKernel\Config\FileLocator;
use ECSPrefix20211030\Symfony\Component\HttpKernel\KernelInterface;
use ECSPrefix20211030\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
final class DelegatingLoaderFactory
{
    public function createFromContainerBuilderAndKernel(\ECSPrefix20211030\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, \ECSPrefix20211030\Symfony\Component\HttpKernel\KernelInterface $kernel) : \ECSPrefix20211030\Symfony\Component\Config\Loader\DelegatingLoader
    {
        $kernelFileLocator = new \ECSPrefix20211030\Symfony\Component\HttpKernel\Config\FileLocator($kernel);
        return $this->createFromContainerBuilderAndFileLocator($containerBuilder, $kernelFileLocator);
    }
    private function createFromContainerBuilderAndFileLocator(\ECSPrefix20211030\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, \ECSPrefix20211030\Symfony\Component\HttpKernel\Config\FileLocator $fileLocator) : \ECSPrefix20211030\Symfony\Component\Config\Loader\DelegatingLoader
    {
        $loaders = [new \ECSPrefix20211030\Symfony\Component\Config\Loader\GlobFileLoader($fileLocator), new \ECSPrefix20211030\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader($containerBuilder, $fileLocator)];
        $loaderResolver = new \ECSPrefix20211030\Symfony\Component\Config\Loader\LoaderResolver($loaders);
        return new \ECSPrefix20211030\Symfony\Component\Config\Loader\DelegatingLoader($loaderResolver);
    }
}
