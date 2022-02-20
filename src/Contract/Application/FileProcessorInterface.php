<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
interface FileProcessorInterface
{
    public function processFileToString(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : string;
    /**
     * @return array<string, array<FileDiff|CodingStandardError>>
     */
    public function processFile(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration) : array;
    /**
     * @return object[]
     */
    public function getCheckers() : array;
}
