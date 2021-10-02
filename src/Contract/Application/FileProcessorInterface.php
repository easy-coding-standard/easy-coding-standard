<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
interface FileProcessorInterface
{
    /**
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     */
    public function processFileToString($smartFileInfo) : string;
    /**
     * @return array<string, array<FileDiff|CodingStandardError>>
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @param \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration
     */
    public function processFile($smartFileInfo, $configuration) : array;
    /**
     * @return object[]
     */
    public function getCheckers() : array;
}
