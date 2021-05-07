<?php

namespace Symplify\EasyCodingStandard\Error;

use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\SmartFileSystem\SmartFileInfo;
final class ErrorFactory
{
    /**
     * @param int $line
     * @param string $message
     * @param string $sourceClass
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @return \Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError
     */
    public function create($line, $message, $sourceClass, $smartFileInfo)
    {
        return new CodingStandardError($line, $message, $sourceClass, $smartFileInfo);
    }
}
