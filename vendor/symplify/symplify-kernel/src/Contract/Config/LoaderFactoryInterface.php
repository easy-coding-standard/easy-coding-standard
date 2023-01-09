<?php

declare (strict_types=1);
namespace ECSPrefix202301\Symplify\SymplifyKernel\Contract\Config;

use ECSPrefix202301\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix202301\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
