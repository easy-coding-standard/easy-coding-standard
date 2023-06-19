<?php

declare (strict_types=1);
namespace ECSPrefix202306\Symplify\SymplifyKernel\Contract\Config;

use ECSPrefix202306\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix202306\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
