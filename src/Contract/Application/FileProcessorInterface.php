<?php

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\SmartFileSystem\SmartFileInfo;
interface FileProcessorInterface
{
    /**
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @return string
     */
    public function processFile($smartFileInfo);
    /**
     * @return mixed[]
     */
    public function getCheckers();
}
