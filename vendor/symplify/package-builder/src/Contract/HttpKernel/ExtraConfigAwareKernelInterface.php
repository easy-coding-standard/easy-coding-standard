<?php

namespace ECSPrefix20210514\Symplify\PackageBuilder\Contract\HttpKernel;

use ECSPrefix20210514\Symfony\Component\HttpKernel\KernelInterface;
use ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo;
interface ExtraConfigAwareKernelInterface extends \ECSPrefix20210514\Symfony\Component\HttpKernel\KernelInterface
{
    /**
     * @param string[]|SmartFileInfo[] $configs
     * @return void
     */
    public function setConfigs(array $configs);
}
