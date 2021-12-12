<?php

declare (strict_types=1);
namespace ECSPrefix20211212\Symplify\SymplifyKernel\Contract\Config;

use ECSPrefix20211212\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20211212\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\ECSPrefix20211212\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \ECSPrefix20211212\Symfony\Component\Config\Loader\LoaderInterface;
}
