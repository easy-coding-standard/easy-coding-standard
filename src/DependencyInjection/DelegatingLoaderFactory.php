<?php

namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20210507\Symfony\Component\Config\FileLocator as SimpleFileLocator;
use ECSPrefix20210507\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20210507\Symfony\Component\Config\Loader\GlobFileLoader;
use ECSPrefix20210507\Symfony\Component\Config\Loader\LoaderResolver;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210507\Symfony\Component\HttpKernel\Config\FileLocator;
use ECSPrefix20210507\Symfony\Component\HttpKernel\KernelInterface;
use Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
final class DelegatingLoaderFactory
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    public function createFromContainerBuilderAndKernel($containerBuilder, $kernel)
    {
        $kernelFileLocator = new \ECSPrefix20210507\Symfony\Component\HttpKernel\Config\FileLocator($kernel);
        return $this->createFromContainerBuilderAndFileLocator($containerBuilder, $kernelFileLocator);
    }
    /**
     * For tests
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     * @param string $config
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    public function createContainerBuilderAndConfig($containerBuilder, $config)
    {
        $directory = \dirname($config);
        $fileLocator = new \ECSPrefix20210507\Symfony\Component\Config\FileLocator($directory);
        return $this->createFromContainerBuilderAndFileLocator($containerBuilder, $fileLocator);
    }
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     * @param SimpleFileLocator $simpleFileLocator
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    private function createFromContainerBuilderAndFileLocator($containerBuilder, $simpleFileLocator)
    {
        $loaders = [new \ECSPrefix20210507\Symfony\Component\Config\Loader\GlobFileLoader($simpleFileLocator), new \Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader($containerBuilder, $simpleFileLocator)];
        $loaderResolver = new \ECSPrefix20210507\Symfony\Component\Config\Loader\LoaderResolver($loaders);
        return new \ECSPrefix20210507\Symfony\Component\Config\Loader\DelegatingLoader($loaderResolver);
    }
}
