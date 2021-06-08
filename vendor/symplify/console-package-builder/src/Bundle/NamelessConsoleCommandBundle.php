<?php

declare (strict_types=1);
namespace ECSPrefix20210608\Symplify\ConsolePackageBuilder\Bundle;

use ECSPrefix20210608\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210608\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210608\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass;
final class NamelessConsoleCommandBundle extends \ECSPrefix20210608\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return void
     */
    public function build(\ECSPrefix20210608\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new \ECSPrefix20210608\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass());
    }
}
