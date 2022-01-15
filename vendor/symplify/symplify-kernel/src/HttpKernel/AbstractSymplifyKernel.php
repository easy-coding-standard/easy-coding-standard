<?php

declare (strict_types=1);
namespace ECSPrefix20220115\Symplify\SymplifyKernel\HttpKernel;

use ECSPrefix20220115\Symfony\Component\DependencyInjection\Container;
use ECSPrefix20220115\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20220115\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use ECSPrefix20220115\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use ECSPrefix20220115\Symplify\SymplifyKernel\ContainerBuilderFactory;
use ECSPrefix20220115\Symplify\SymplifyKernel\Contract\LightKernelInterface;
use ECSPrefix20220115\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use ECSPrefix20220115\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;
/**
 * @api
 */
abstract class AbstractSymplifyKernel implements \ECSPrefix20220115\Symplify\SymplifyKernel\Contract\LightKernelInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container|null
     */
    private $container = null;
    /**
     * @param string[] $configFiles
     */
    public function create(array $extensions, array $compilerPasses, array $configFiles) : \ECSPrefix20220115\Symfony\Component\DependencyInjection\ContainerInterface
    {
        $containerBuilderFactory = new \ECSPrefix20220115\Symplify\SymplifyKernel\ContainerBuilderFactory(new \ECSPrefix20220115\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory());
        $compilerPasses[] = new \ECSPrefix20220115\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass();
        $configFiles[] = \ECSPrefix20220115\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig::FILE_PATH;
        $containerBuilder = $containerBuilderFactory->create($extensions, $compilerPasses, $configFiles);
        $containerBuilder->compile();
        $this->container = $containerBuilder;
        return $containerBuilder;
    }
    public function getContainer() : \ECSPrefix20220115\Psr\Container\ContainerInterface
    {
        if (!$this->container instanceof \ECSPrefix20220115\Symfony\Component\DependencyInjection\Container) {
            throw new \ECSPrefix20220115\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $this->container;
    }
}
