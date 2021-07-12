<?php

declare (strict_types=1);
namespace ECSPrefix20210712\Symplify\SymplifyKernel\Bundle;

use ECSPrefix20210712\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210712\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210712\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use ECSPrefix20210712\Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension;
final class SymplifyKernelBundle extends \ECSPrefix20210712\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     * @return void
     */
    public function build($containerBuilder)
    {
        $containerBuilder->addCompilerPass(new \ECSPrefix20210712\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass());
    }
    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new \ECSPrefix20210712\Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension();
    }
}
