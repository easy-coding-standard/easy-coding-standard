<?php

declare (strict_types=1);
namespace ECSPrefix20211116\Symplify\SymplifyKernel\Contract;

use ECSPrefix20211116\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs($configFiles) : \ECSPrefix20211116\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20211116\Psr\Container\ContainerInterface;
}
