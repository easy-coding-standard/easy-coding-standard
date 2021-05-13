<?php

namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20210513\Symfony\Component\Config\FileLocator as SimpleFileLocator;
use ECSPrefix20210513\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20210513\Symfony\Component\Config\Loader\GlobFileLoader;
use ECSPrefix20210513\Symfony\Component\Config\Loader\LoaderResolver;
use ECSPrefix20210513\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210513\Symfony\Component\HttpKernel\Config\FileLocator;
use ECSPrefix20210513\Symfony\Component\HttpKernel\KernelInterface;
use Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
final class DelegatingLoaderFactory
{
    /**
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    public function createFromContainerBuilderAndKernel(\ECSPrefix20210513\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, \ECSPrefix20210513\Symfony\Component\HttpKernel\KernelInterface $kernel)
    {
        $kernelFileLocator = new \ECSPrefix20210513\Symfony\Component\HttpKernel\Config\FileLocator($kernel);
        return $this->createFromContainerBuilderAndFileLocator($containerBuilder, $kernelFileLocator);
    }
    /**
     * For tests
     * @param string $config
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    public function createContainerBuilderAndConfig(\ECSPrefix20210513\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, $config)
    {
        $config = (string) $config;
        $directory = \dirname($config);
        $fileLocator = new \ECSPrefix20210513\Symfony\Component\Config\FileLocator($directory);
        return $this->createFromContainerBuilderAndFileLocator($containerBuilder, $fileLocator);
    }
    /**
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    private function createFromContainerBuilderAndFileLocator(\ECSPrefix20210513\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, \ECSPrefix20210513\Symfony\Component\Config\FileLocator $simpleFileLocator)
    {
        $loaders = [new \ECSPrefix20210513\Symfony\Component\Config\Loader\GlobFileLoader($simpleFileLocator), new \Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader($containerBuilder, $simpleFileLocator)];
        $loaderResolver = new \ECSPrefix20210513\Symfony\Component\Config\Loader\LoaderResolver($loaders);
        return new \ECSPrefix20210513\Symfony\Component\Config\Loader\DelegatingLoader($loaderResolver);
    }
}
