<?php

declare (strict_types=1);
namespace ECSPrefix20211101\Symplify\PackageBuilder\Bundle;

use ECSPrefix20211101\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20211101\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20211101\Symplify\PackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass;
final class NamelessConsoleCommandBundle extends \ECSPrefix20211101\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function build($containerBuilder) : void
    {
        $containerBuilder->addCompilerPass(new \ECSPrefix20211101\Symplify\PackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass());
    }
}
