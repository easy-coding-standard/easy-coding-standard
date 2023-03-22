<?php

declare (strict_types=1);
namespace ECSPrefix202303\Symplify\SymplifyKernel\Contract\Config;

use ECSPrefix202303\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix202303\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : LoaderInterface;
}
