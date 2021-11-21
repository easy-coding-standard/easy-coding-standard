<?php

declare (strict_types=1);
namespace ECSPrefix20211121\Symplify\SymplifyKernel\Contract;

use ECSPrefix20211121\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs($configFiles) : \ECSPrefix20211121\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20211121\Psr\Container\ContainerInterface;
}
