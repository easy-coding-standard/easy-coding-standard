<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Config\FileLocator as SimpleFileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\EasyCodingStandard\Yaml\CheckerTolerantYamlFileLoader;

final class DelegatingLoaderFactory
{
    public function createFromContainerBuilderAndKernel(ContainerBuilder $containerBuilder, KernelInterface $kernel): DelegatingLoader
    {
        return $this->createFromFileLocator($containerBuilder, new FileLocator($kernel));
    }

    public function createContainerBuilderAndConfig(ContainerBuilder $containerBuilder, string $config): DelegatingLoader
    {
        return $this->createFromFileLocator($containerBuilder, new SimpleFileLocator(dirname($config)));
    }

    private function createFromFileLocator(ContainerBuilder $containerBuilder, SimpleFileLocator $fileLocator): DelegatingLoader
    {
        $loaderResolver = new LoaderResolver([
            new GlobFileLoader($containerBuilder, $fileLocator),
            new CheckerTolerantYamlFileLoader($containerBuilder, $fileLocator),
        ]);

        return new DelegatingLoader($loaderResolver);
    }
}
