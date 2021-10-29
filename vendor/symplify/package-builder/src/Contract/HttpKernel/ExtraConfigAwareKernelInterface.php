<?php

declare (strict_types=1);
namespace ECSPrefix20211029\Symplify\PackageBuilder\Contract\HttpKernel;

use ECSPrefix20211029\Symfony\Component\HttpKernel\KernelInterface;
use ECSPrefix20211029\Symplify\SmartFileSystem\SmartFileInfo;
interface ExtraConfigAwareKernelInterface extends \ECSPrefix20211029\Symfony\Component\HttpKernel\KernelInterface
{
    /**
     * @param string[]|SmartFileInfo[] $configs
     */
    public function setConfigs($configs) : void;
}
