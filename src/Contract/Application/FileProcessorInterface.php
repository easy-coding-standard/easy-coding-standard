<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix20210619\Symplify\SmartFileSystem\SmartFileInfo;
interface FileProcessorInterface
{
    public function processFileToString(\ECSPrefix20210619\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : string;
    /**
     * @return array<FileDiff>
     */
    public function processFile(\ECSPrefix20210619\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : array;
    /**
     * @return object[]
     */
    public function getCheckers() : array;
}
