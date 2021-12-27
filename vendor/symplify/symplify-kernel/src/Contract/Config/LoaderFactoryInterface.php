<?php

declare (strict_types=1);
namespace ECSPrefix20211227\Symplify\SymplifyKernel\Contract\Config;

use ECSPrefix20211227\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20211227\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\ECSPrefix20211227\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \ECSPrefix20211227\Symfony\Component\Config\Loader\LoaderInterface;
}
