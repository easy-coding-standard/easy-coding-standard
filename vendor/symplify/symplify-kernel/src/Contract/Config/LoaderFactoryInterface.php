<?php

declare (strict_types=1);
namespace ECSPrefix202212\Symplify\SymplifyKernel\Contract\Config;

use ECSPrefix202212\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix202212\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
