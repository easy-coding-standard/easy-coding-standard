<?php

namespace Symplify\SymplifyKernel\Bundle;

use ECSPrefix20210513\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210513\Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension;
final class SymplifyKernelBundle extends \ECSPrefix20210513\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return void
     */
    public function build(\ECSPrefix20210513\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new \Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass());
    }
    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new \Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension();
    }
}
