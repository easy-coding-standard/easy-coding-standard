<?php

declare (strict_types=1);
namespace ECSPrefix20220605\Symplify\SymplifyKernel\Contract\Config;

use ECSPrefix20220605\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20220605\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\ECSPrefix20220605\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \ECSPrefix20220605\Symfony\Component\Config\Loader\LoaderInterface;
}
