<?php

declare (strict_types=1);
namespace ECSPrefix202306\Symplify\SymplifyKernel\Contract;

use ECSPrefix202306\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface;
    public function getContainer() : ContainerInterface;
}
