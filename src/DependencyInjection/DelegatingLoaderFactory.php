<?php

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Config\FileLocator as SimpleFileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;

final class DelegatingLoaderFactory
{
    /**
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    public function createFromContainerBuilderAndKernel(
        ContainerBuilder $containerBuilder,
        KernelInterface $kernel
    ) {
        $kernelFileLocator = new FileLocator($kernel);

        return $this->createFromContainerBuilderAndFileLocator($containerBuilder, $kernelFileLocator);
    }

    /**
     * For tests
     * @param string $config
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    public function createContainerBuilderAndConfig(
        ContainerBuilder $containerBuilder,
        $config
    ) {
        $config = (string) $config;
        $directory = dirname($config);
        $fileLocator = new SimpleFileLocator($directory);

        return $this->createFromContainerBuilderAndFileLocator($containerBuilder, $fileLocator);
    }

    /**
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    private function createFromContainerBuilderAndFileLocator(
        ContainerBuilder $containerBuilder,
        SimpleFileLocator $simpleFileLocator
    ) {
        $loaders = [
            new GlobFileLoader($simpleFileLocator),
            new ParameterMergingPhpFileLoader($containerBuilder, $simpleFileLocator),
        ];
        $loaderResolver = new LoaderResolver($loaders);

        return new DelegatingLoader($loaderResolver);
    }
}
