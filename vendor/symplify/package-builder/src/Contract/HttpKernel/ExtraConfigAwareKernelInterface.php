<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PackageBuilder\Contract\HttpKernel;

use ConfigTransformer20210601\Symfony\Component\HttpKernel\KernelInterface;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
interface ExtraConfigAwareKernelInterface extends \ConfigTransformer20210601\Symfony\Component\HttpKernel\KernelInterface
{
    /**
     * @param string[]|SmartFileInfo[] $configs
     * @return void
     */
    public function setConfigs(array $configs);
}
