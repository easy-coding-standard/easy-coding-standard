<?php

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\SmartFileSystem\SmartFileInfo;

interface FileProcessorInterface
{
    /**
     * @return string
     */
    public function processFile(SmartFileInfo $smartFileInfo);

    /**
     * @return mixed[]
     */
    public function getCheckers();
}
