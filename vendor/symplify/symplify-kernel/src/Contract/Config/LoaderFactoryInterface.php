<?php

declare (strict_types=1);
namespace ECSPrefix20220531\Symplify\SymplifyKernel\Contract\Config;

use ECSPrefix20220531\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20220531\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\ECSPrefix20220531\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \ECSPrefix20220531\Symfony\Component\Config\Loader\LoaderInterface;
}
