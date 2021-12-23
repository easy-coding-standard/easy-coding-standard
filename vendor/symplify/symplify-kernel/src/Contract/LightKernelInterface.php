<?php

declare (strict_types=1);
namespace ECSPrefix20211223\Symplify\SymplifyKernel\Contract;

use ECSPrefix20211223\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20211223\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20211223\Psr\Container\ContainerInterface;
}
