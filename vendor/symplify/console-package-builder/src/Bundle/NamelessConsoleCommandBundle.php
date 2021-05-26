<?php

declare (strict_types=1);
namespace ECSPrefix20210526\Symplify\ConsolePackageBuilder\Bundle;

use ECSPrefix20210526\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210526\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210526\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass;
final class NamelessConsoleCommandBundle extends \ECSPrefix20210526\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return void
     */
    public function build(\ECSPrefix20210526\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new \ECSPrefix20210526\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass());
    }
}
