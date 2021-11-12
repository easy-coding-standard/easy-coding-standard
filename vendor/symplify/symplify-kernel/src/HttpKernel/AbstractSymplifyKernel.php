<?php

declare (strict_types=1);
namespace ECSPrefix20211112\Symplify\SymplifyKernel\HttpKernel;

use ECSPrefix20211112\Symfony\Component\DependencyInjection\Container;
use ECSPrefix20211112\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20211112\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use ECSPrefix20211112\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use ECSPrefix20211112\Symplify\SymplifyKernel\ContainerBuilderFactory;
use ECSPrefix20211112\Symplify\SymplifyKernel\Contract\LightKernelInterface;
use ECSPrefix20211112\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use ECSPrefix20211112\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;
/**
 * @api
 */
abstract class AbstractSymplifyKernel implements \ECSPrefix20211112\Symplify\SymplifyKernel\Contract\LightKernelInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container|null
     */
    private $container = null;
    /**
     * @param string[] $configFiles
     * @param mixed[] $extensions
     * @param mixed[] $compilerPasses
     */
    public function create($extensions, $compilerPasses, $configFiles) : \ECSPrefix20211112\Symfony\Component\DependencyInjection\ContainerInterface
    {
        $containerBuilderFactory = new \ECSPrefix20211112\Symplify\SymplifyKernel\ContainerBuilderFactory(new \ECSPrefix20211112\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory());
        $compilerPasses[] = new \ECSPrefix20211112\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass();
        $configFiles[] = \ECSPrefix20211112\Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig::FILE_PATH;
        $containerBuilder = $containerBuilderFactory->create($extensions, $compilerPasses, $configFiles);
        $containerBuilder->compile();
        $this->container = $containerBuilder;
        return $containerBuilder;
    }
    public function getContainer() : \ECSPrefix20211112\Psr\Container\ContainerInterface
    {
        if (!$this->container instanceof \ECSPrefix20211112\Symfony\Component\DependencyInjection\Container) {
            throw new \ECSPrefix20211112\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $this->container;
    }
}
