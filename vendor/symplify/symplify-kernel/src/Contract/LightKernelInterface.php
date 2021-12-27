<?php

declare (strict_types=1);
namespace ECSPrefix20211227\Symplify\SymplifyKernel\Contract;

use ECSPrefix20211227\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20211227\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20211227\Psr\Container\ContainerInterface;
}
