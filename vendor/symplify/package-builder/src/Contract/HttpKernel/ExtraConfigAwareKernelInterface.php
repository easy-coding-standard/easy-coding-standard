<?php

namespace ECSPrefix20210516\Symplify\PackageBuilder\Contract\HttpKernel;

use ECSPrefix20210516\Symfony\Component\HttpKernel\KernelInterface;
use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo;
interface ExtraConfigAwareKernelInterface extends \ECSPrefix20210516\Symfony\Component\HttpKernel\KernelInterface
{
    /**
     * @param string[]|SmartFileInfo[] $configs
     * @return void
     */
    public function setConfigs(array $configs);
}
