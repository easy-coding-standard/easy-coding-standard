<?php

declare (strict_types=1);
namespace ECSPrefix20220124\Symplify\SymplifyKernel\Contract;

use ECSPrefix20220124\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20220124\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20220124\Psr\Container\ContainerInterface;
}
