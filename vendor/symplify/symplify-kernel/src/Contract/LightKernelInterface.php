<?php

declare (strict_types=1);
namespace ECSPrefix20211130\Symplify\SymplifyKernel\Contract;

use ECSPrefix20211130\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs($configFiles) : \ECSPrefix20211130\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20211130\Psr\Container\ContainerInterface;
}
