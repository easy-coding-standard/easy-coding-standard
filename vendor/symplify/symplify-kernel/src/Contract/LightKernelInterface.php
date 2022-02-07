<?php

declare (strict_types=1);
namespace ECSPrefix20220207\Symplify\SymplifyKernel\Contract;

use ECSPrefix20220207\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20220207\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20220207\Psr\Container\ContainerInterface;
}
