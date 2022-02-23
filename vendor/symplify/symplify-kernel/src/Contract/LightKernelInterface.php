<?php

declare (strict_types=1);
namespace ECSPrefix20220223\Symplify\SymplifyKernel\Contract;

use ECSPrefix20220223\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20220223\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20220223\Psr\Container\ContainerInterface;
}
