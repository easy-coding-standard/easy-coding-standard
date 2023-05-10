<?php

declare (strict_types=1);
namespace ECSPrefix202305\Symplify\SymplifyKernel\Contract;

use ECSPrefix202305\Psr\Container\ContainerInterface;
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
