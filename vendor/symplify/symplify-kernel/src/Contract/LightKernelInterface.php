<?php

declare (strict_types=1);
namespace ECSPrefix20211231\Symplify\SymplifyKernel\Contract;

use ECSPrefix20211231\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20211231\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20211231\Psr\Container\ContainerInterface;
}
