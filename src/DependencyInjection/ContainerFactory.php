<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

final class ContainerFactory
{
    /**
     * @return ContainerInterface|SymfonyContainerInterface|Container
     */
    public function create(): ContainerInterface
    {
        $appKernel = new EasyCodingStandardKernel();
        $appKernel->boot();

        return $appKernel->getContainer();
    }

    /**
     * @return ContainerInterface|SymfonyContainerInterface|Container
     */
    public function createWithConfig(string $config): ContainerInterface
    {
        $kernel = new EasyCodingStandardKernel();
        $kernel->addConfigFile($config);
        $kernel->boot();

        return $kernel->getContainer();
    }
}
