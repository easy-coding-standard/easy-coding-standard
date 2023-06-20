<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Kernel;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Webmozart\Assert\Assert;

final class ContainerBuilderFactory
{
    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     */
    public function create(array $configFiles, array $compilerPasses): ContainerBuilder
    {
        Assert::allString($configFiles);
        Assert::allFile($configFiles);

        $containerBuilder = new ContainerBuilder();
        $loaderResolver = $this->createLoaderResolver($containerBuilder);

        $delegatingLoader = new DelegatingLoader($loaderResolver);
        foreach ($configFiles as $configFile) {
            $delegatingLoader->load($configFile);
        }

        foreach ($compilerPasses as $compilerPass) {
            $containerBuilder->addCompilerPass($compilerPass);
        }

        return $containerBuilder;
    }

    private function createLoaderResolver(ContainerBuilder $containerBuilder): LoaderResolver
    {
        $fileLocator = new FileLocator([getcwd()]);

        $loaders = [new GlobFileLoader($fileLocator), new PhpFileLoader($containerBuilder, $fileLocator)];

        return new LoaderResolver($loaders);
    }
}
