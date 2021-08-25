<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20210825\Symfony\Component\Config\FileLocator as SimpleFileLocator;
use ECSPrefix20210825\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20210825\Symfony\Component\Config\Loader\GlobFileLoader;
use ECSPrefix20210825\Symfony\Component\Config\Loader\LoaderResolver;
use ECSPrefix20210825\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210825\Symfony\Component\HttpKernel\Config\FileLocator;
use ECSPrefix20210825\Symfony\Component\HttpKernel\KernelInterface;
use ECSPrefix20210825\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
final class DelegatingLoaderFactory
{
    public function createFromContainerBuilderAndKernel(\ECSPrefix20210825\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, \ECSPrefix20210825\Symfony\Component\HttpKernel\KernelInterface $kernel) : \ECSPrefix20210825\Symfony\Component\Config\Loader\DelegatingLoader
    {
        $kernelFileLocator = new \ECSPrefix20210825\Symfony\Component\HttpKernel\Config\FileLocator($kernel);
        return $this->createFromContainerBuilderAndFileLocator($containerBuilder, $kernelFileLocator);
    }
    /**
     * For tests
     */
    public function createContainerBuilderAndConfig(\ECSPrefix20210825\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $config) : \ECSPrefix20210825\Symfony\Component\Config\Loader\DelegatingLoader
    {
        $directory = \dirname($config);
        $fileLocator = new \ECSPrefix20210825\Symfony\Component\Config\FileLocator($directory);
        return $this->createFromContainerBuilderAndFileLocator($containerBuilder, $fileLocator);
    }
    private function createFromContainerBuilderAndFileLocator(\ECSPrefix20210825\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, \ECSPrefix20210825\Symfony\Component\Config\FileLocator $simpleFileLocator) : \ECSPrefix20210825\Symfony\Component\Config\Loader\DelegatingLoader
    {
        $loaders = [new \ECSPrefix20210825\Symfony\Component\Config\Loader\GlobFileLoader($simpleFileLocator), new \ECSPrefix20210825\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader($containerBuilder, $simpleFileLocator)];
        $loaderResolver = new \ECSPrefix20210825\Symfony\Component\Config\Loader\LoaderResolver($loaders);
        return new \ECSPrefix20210825\Symfony\Component\Config\Loader\DelegatingLoader($loaderResolver);
    }
}
