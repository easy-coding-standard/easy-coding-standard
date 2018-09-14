<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;
use function Safe\putenv;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $kernel = new EasyCodingStandardKernel();
        $kernel->boot();

        // this is require to keep CLI verbosity independent on AppKernel dev/prod mode
        putenv('SHELL_VERBOSITY=0');

        return $kernel->getContainer();
    }

    /**
     * @param string[] $configs
     */
    public function createWithConfigs(array $configs): ContainerInterface
    {
        $kernel = new EasyCodingStandardKernel($configs);
        $kernel->boot();

        // this is require to keep CLI verbosity independent on AppKernel dev/prod mode
        putenv('SHELL_VERBOSITY=0');

        return $kernel->getContainer();
    }
}
