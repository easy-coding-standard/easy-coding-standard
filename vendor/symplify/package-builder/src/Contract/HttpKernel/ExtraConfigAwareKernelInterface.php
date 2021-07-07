<?php

declare (strict_types=1);
namespace ECSPrefix20210707\Symplify\PackageBuilder\Contract\HttpKernel;

use ECSPrefix20210707\Symfony\Component\HttpKernel\KernelInterface;
use ECSPrefix20210707\Symplify\SmartFileSystem\SmartFileInfo;
interface ExtraConfigAwareKernelInterface extends \ECSPrefix20210707\Symfony\Component\HttpKernel\KernelInterface
{
    /**
     * @param string[]|SmartFileInfo[] $configs
     * @return void
     */
    public function setConfigs(array $configs);
}
