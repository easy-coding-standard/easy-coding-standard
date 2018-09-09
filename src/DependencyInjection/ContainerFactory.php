<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $appKernel = new EasyCodingStandardKernel();
        $appKernel->boot();

        return $appKernel->getContainer();
    }

    /**
     * @param string[] $configs
     */
    public function createWithConfigs(array $configs): ContainerInterface
    {
        $kernel = new EasyCodingStandardKernel($configs);
        $kernel->boot();

        return $kernel->getContainer();
    }
}
