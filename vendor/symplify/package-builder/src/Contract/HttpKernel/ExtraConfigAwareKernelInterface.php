<?php

declare (strict_types=1);
namespace ECSPrefix20210710\Symplify\PackageBuilder\Contract\HttpKernel;

use ECSPrefix20210710\Symfony\Component\HttpKernel\KernelInterface;
use ECSPrefix20210710\Symplify\SmartFileSystem\SmartFileInfo;
interface ExtraConfigAwareKernelInterface extends \ECSPrefix20210710\Symfony\Component\HttpKernel\KernelInterface
{
    /**
     * @param string[]|SmartFileInfo[] $configs
     * @return void
     */
    public function setConfigs($configs);
}
