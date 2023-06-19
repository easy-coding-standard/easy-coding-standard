<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Kernel;

use ECSPrefix202306\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix202306\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix202306\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;
use ECSPrefix202306\Webmozart\Assert\Assert;
final class ContainerBuilderFactory
{
    /**
     * @readonly
     * @var \Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface
     */
    private $loaderFactory;
    public function __construct(LoaderFactoryInterface $loaderFactory)
    {
        $this->loaderFactory = $loaderFactory;
    }
    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     */
    public function create(array $configFiles, array $compilerPasses) : ContainerBuilder
    {
        Assert::allString($configFiles);
        Assert::allFile($configFiles);
        $containerBuilder = new ContainerBuilder();
        $delegatingLoader = $this->loaderFactory->create($containerBuilder, \getcwd());
        foreach ($configFiles as $configFile) {
            $delegatingLoader->load($configFile);
        }
        foreach ($compilerPasses as $compilerPass) {
            $containerBuilder->addCompilerPass($compilerPass);
        }
        return $containerBuilder;
    }
}
