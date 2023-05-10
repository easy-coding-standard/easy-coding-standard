<?php

declare (strict_types=1);
namespace ECSPrefix202305\Symplify\SymplifyKernel\HttpKernel;

use ECSPrefix202305\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix202305\Symfony\Component\DependencyInjection\Container;
use ECSPrefix202305\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix202305\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use ECSPrefix202305\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use ECSPrefix202305\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use ECSPrefix202305\Symplify\SymplifyKernel\ContainerBuilderFactory;
use ECSPrefix202305\Symplify\SymplifyKernel\Contract\LightKernelInterface;
use ECSPrefix202305\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use ECSPrefix202305\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;
/**
 * @api
 */
abstract class AbstractSymplifyKernel implements LightKernelInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container|null
     */
    private $container = null;
    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     * @param ExtensionInterface[] $extensions
     */
    public function create(array $configFiles, array $compilerPasses = [], array $extensions = []) : ContainerInterface
    {
        $containerBuilderFactory = new ContainerBuilderFactory(new ParameterMergingLoaderFactory());
        $compilerPasses[] = new AutowireArrayParameterCompilerPass();
        $configFiles[] = SymplifyKernelConfig::FILE_PATH;
        $containerBuilder = $containerBuilderFactory->create($configFiles, $compilerPasses, $extensions);
        $containerBuilder->compile();
        $this->container = $containerBuilder;
        return $containerBuilder;
    }
    public function getContainer() : \ECSPrefix202305\Psr\Container\ContainerInterface
    {
        if (!$this->container instanceof Container) {
            throw new ShouldNotHappenException();
        }
        return $this->container;
    }
}
