<?php

declare (strict_types=1);
namespace ECSPrefix20210825\Symplify\ConsolePackageBuilder\Bundle;

use ECSPrefix20210825\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210825\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210825\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass;
final class NamelessConsoleCommandBundle extends \ECSPrefix20210825\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function build($containerBuilder) : void
    {
        $containerBuilder->addCompilerPass(new \ECSPrefix20210825\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass());
    }
}
