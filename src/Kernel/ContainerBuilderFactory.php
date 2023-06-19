<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Kernel;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;
use Webmozart\Assert\Assert;

final class ContainerBuilderFactory
{
    public function __construct(
        private readonly LoaderFactoryInterface $loaderFactory
    ) {
    }

    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     */
    public function create(array $configFiles, array $compilerPasses): ContainerBuilder
    {
        Assert::allString($configFiles);
        Assert::allFile($configFiles);

        $containerBuilder = new ContainerBuilder();

        $delegatingLoader = $this->loaderFactory->create($containerBuilder, getcwd());
        foreach ($configFiles as $configFile) {
            $delegatingLoader->load($configFile);
        }

        foreach ($compilerPasses as $compilerPass) {
            $containerBuilder->addCompilerPass($compilerPass);
        }

        return $containerBuilder;
    }
}
