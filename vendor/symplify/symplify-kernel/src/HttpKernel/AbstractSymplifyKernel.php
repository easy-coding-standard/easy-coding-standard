<?php

declare (strict_types=1);
namespace ECSPrefix20220503\Symplify\SymplifyKernel\HttpKernel;

use ECSPrefix20220503\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20220503\Symfony\Component\DependencyInjection\Container;
use ECSPrefix20220503\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20220503\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use ECSPrefix20220503\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use ECSPrefix20220503\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use ECSPrefix20220503\Symplify\SymplifyKernel\ContainerBuilderFactory;
use ECSPrefix20220503\Symplify\SymplifyKernel\Contract\LightKernelInterface;
use ECSPrefix20220503\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use ECSPrefix20220503\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;
/**
 * @api
 */
abstract class AbstractSymplifyKernel implements \ECSPrefix20220503\Symplify\SymplifyKernel\Contract\LightKernelInterface
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
    public function create(array $configFiles, array $compilerPasses = [], array $extensions = []) : \ECSPrefix20220503\Symfony\Component\DependencyInjection\ContainerInterface
    {
        $containerBuilderFactory = new \ECSPrefix20220503\Symplify\SymplifyKernel\ContainerBuilderFactory(new \ECSPrefix20220503\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory());
        $compilerPasses[] = new \ECSPrefix20220503\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass();
        $configFiles[] = \ECSPrefix20220503\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig::FILE_PATH;
        $containerBuilder = $containerBuilderFactory->create($configFiles, $compilerPasses, $extensions);
        $containerBuilder->compile();
        $this->container = $containerBuilder;
        return $containerBuilder;
    }
    public function getContainer() : \ECSPrefix20220503\Psr\Container\ContainerInterface
    {
        if (!$this->container instanceof \ECSPrefix20220503\Symfony\Component\DependencyInjection\Container) {
            throw new \ECSPrefix20220503\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $this->container;
    }
}
