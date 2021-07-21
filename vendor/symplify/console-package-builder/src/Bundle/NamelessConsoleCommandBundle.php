<?php

declare (strict_types=1);
namespace ECSPrefix20210721\Symplify\ConsolePackageBuilder\Bundle;

use ECSPrefix20210721\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210721\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210721\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass;
final class NamelessConsoleCommandBundle extends \ECSPrefix20210721\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     * @return void
     */
    public function build($containerBuilder)
    {
        $containerBuilder->addCompilerPass(new \ECSPrefix20210721\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass());
    }
}
