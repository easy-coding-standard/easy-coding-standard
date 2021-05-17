<?php

namespace Symplify\EasyCodingStandard\Contract\Application;

use ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo;
interface FileProcessorInterface
{
    /**
     * @return string
     */
    public function processFile(\ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo);
    /**
     * @return mixed[]
     */
    public function getCheckers();
}
