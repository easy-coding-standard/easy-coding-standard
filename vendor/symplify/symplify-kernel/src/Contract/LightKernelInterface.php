<?php

declare (strict_types=1);
namespace ECSPrefix20211208\Symplify\SymplifyKernel\Contract;

use ECSPrefix20211208\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs($configFiles) : \ECSPrefix20211208\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20211208\Psr\Container\ContainerInterface;
}
