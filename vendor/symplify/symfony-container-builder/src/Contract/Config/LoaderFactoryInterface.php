<?php

declare (strict_types=1);
namespace ECSPrefix20211101\Symplify\SymfonyContainerBuilder\Contract\Config;

use ECSPrefix20211101\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20211101\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     * @param string $currentWorkingDirectory
     */
    public function create($containerBuilder, $currentWorkingDirectory) : \ECSPrefix20211101\Symfony\Component\Config\Loader\LoaderInterface;
}
