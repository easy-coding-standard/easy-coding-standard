<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\Contract\Application;

use ECSPrefix20220607\Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Configuration;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix20220607\Symplify\SmartFileSystem\SmartFileInfo;
interface FileProcessorInterface
{
    public function processFileToString(SmartFileInfo $smartFileInfo) : string;
    /**
     * @return array{file_diffs?: FileDiff[], coding_standard_errors?: CodingStandardError[]}
     */
    public function processFile(SmartFileInfo $smartFileInfo, Configuration $configuration) : array;
    /**
     * @return object[]
     */
    public function getCheckers() : array;
}
