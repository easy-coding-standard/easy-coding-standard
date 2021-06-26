<?php

declare (strict_types=1);
namespace ECSPrefix20210626\Symplify\SymplifyKernel\Bundle;

use ECSPrefix20210626\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210626\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210626\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use ECSPrefix20210626\Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension;
final class SymplifyKernelBundle extends \ECSPrefix20210626\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return void
     */
    public function build(\ECSPrefix20210626\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new \ECSPrefix20210626\Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass());
    }
    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new \ECSPrefix20210626\Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension();
    }
}
