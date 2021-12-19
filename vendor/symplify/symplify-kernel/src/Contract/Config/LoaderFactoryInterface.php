<?php

declare (strict_types=1);
namespace ECSPrefix20211219\Symplify\SymplifyKernel\Contract\Config;

use ECSPrefix20211219\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20211219\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\ECSPrefix20211219\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \ECSPrefix20211219\Symfony\Component\Config\Loader\LoaderInterface;
}
