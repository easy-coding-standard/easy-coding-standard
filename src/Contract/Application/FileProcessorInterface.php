<?php

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\SmartFileSystem\SmartFileInfo;
interface FileProcessorInterface
{
    /**
     * @return string
     */
    public function processFile(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo);
    /**
     * @return mixed[]
     */
    public function getCheckers();
}
