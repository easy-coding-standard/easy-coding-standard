<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Contract\Application;

use ECSPrefix20210607\Symplify\SmartFileSystem\SmartFileInfo;
interface FileProcessorInterface
{
    public function processFile(\ECSPrefix20210607\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : string;
    /**
     * @return object[]
     */
    public function getCheckers() : array;
}
