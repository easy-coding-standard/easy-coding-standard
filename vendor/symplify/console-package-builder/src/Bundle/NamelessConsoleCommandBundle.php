<?php

declare (strict_types=1);
namespace ECSPrefix20210803\Symplify\ConsolePackageBuilder\Bundle;

use ECSPrefix20210803\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210803\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210803\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass;
final class NamelessConsoleCommandBundle extends \ECSPrefix20210803\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     * @return void
     */
    public function build($containerBuilder)
    {
        $containerBuilder->addCompilerPass(new \ECSPrefix20210803\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass());
    }
}
