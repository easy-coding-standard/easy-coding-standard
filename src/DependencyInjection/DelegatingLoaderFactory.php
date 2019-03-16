<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Config\FileLocator as SimpleFileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\EasyCodingStandard\Yaml\FileLoader\CheckerTolerantYamlFileLoader;

final class DelegatingLoaderFactory
{
    public function createFromContainerBuilderAndKernel(
        ContainerBuilder $containerBuilder,
        KernelInterface $kernel
    ): DelegatingLoader {
        return $this->createFromContainerBuilderAndFileLocator($containerBuilder, new FileLocator($kernel));
    }

    /**
     * For tests
     */
    public function createContainerBuilderAndConfig(
        ContainerBuilder $containerBuilder,
        string $config
    ): DelegatingLoader {
        $fileLocator = new SimpleFileLocator(dirname($config));

        return $this->createFromContainerBuilderAndFileLocator($containerBuilder, $fileLocator);
    }

    private function createFromContainerBuilderAndFileLocator(
        ContainerBuilder $containerBuilder,
        SimpleFileLocator $simpleFileLocator
    ): DelegatingLoader {
        $loaderResolver = new LoaderResolver([
            new GlobFileLoader($simpleFileLocator),
            new CheckerTolerantYamlFileLoader($containerBuilder, $simpleFileLocator),
        ]);

        return new DelegatingLoader($loaderResolver);
    }
}
