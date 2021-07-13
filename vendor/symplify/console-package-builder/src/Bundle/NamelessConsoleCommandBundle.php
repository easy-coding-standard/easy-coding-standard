<?php

declare (strict_types=1);
namespace ECSPrefix20210713\Symplify\ConsolePackageBuilder\Bundle;

use ECSPrefix20210713\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210713\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210713\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass;
final class NamelessConsoleCommandBundle extends \ECSPrefix20210713\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     * @return void
     */
    public function build($containerBuilder)
    {
        $containerBuilder->addCompilerPass(new \ECSPrefix20210713\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass());
    }
}
