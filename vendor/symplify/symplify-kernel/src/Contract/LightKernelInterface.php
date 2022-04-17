<?php

declare (strict_types=1);
namespace ECSPrefix20220417\Symplify\SymplifyKernel\Contract;

use ECSPrefix20220417\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20220417\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20220417\Psr\Container\ContainerInterface;
}
