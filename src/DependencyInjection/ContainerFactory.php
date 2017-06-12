<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $appKernel = new AppKernel;
        $appKernel->boot();

        return $appKernel->getContainer();
    }

    public function createWithCustomConfig(string $customConfig): ContainerInterface
    {
        $appKernel = new AppKernel($customConfig);
        $appKernel->boot();

        return $appKernel->getContainer();
    }
}
