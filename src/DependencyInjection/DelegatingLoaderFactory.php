<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\EasyCodingStandard\Yaml\CheckerTolerantYamlFileLoader;

final class DelegatingLoaderFactory
{
    public function createFromKernelAndContainerBuilder(KernelInterface $kernel, ContainerBuilder $containerBuilder): DelegatingLoader
    {
        $fileLocator = new FileLocator($kernel);

        return new DelegatingLoader(new LoaderResolver([
            new GlobFileLoader($containerBuilder, $fileLocator),
            new CheckerTolerantYamlFileLoader($containerBuilder, $fileLocator),
        ]));
    }
}
