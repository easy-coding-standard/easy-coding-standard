<?php

declare (strict_types=1);
namespace ECSPrefix20210707\Symplify\ConsolePackageBuilder\Bundle;

use ECSPrefix20210707\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210707\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210707\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass;
final class NamelessConsoleCommandBundle extends \ECSPrefix20210707\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return void
     */
    public function build(\ECSPrefix20210707\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new \ECSPrefix20210707\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass());
    }
}
