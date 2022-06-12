<?php

declare (strict_types=1);
namespace ECSPrefix20220612\Symplify\SymplifyKernel\Contract\Config;

use ECSPrefix20220612\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20220612\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
