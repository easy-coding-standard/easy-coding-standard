<?php

declare (strict_types=1);
namespace ECSPrefix20211112\Symplify\SymplifyKernel\Contract\Config;

use ECSPrefix20211112\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20211112\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     * @param string $currentWorkingDirectory
     */
    public function create($containerBuilder, $currentWorkingDirectory) : \ECSPrefix20211112\Symfony\Component\Config\Loader\LoaderInterface;
}
