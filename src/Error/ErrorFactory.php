<?php

namespace Symplify\EasyCodingStandard\Error;

use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo;
final class ErrorFactory
{
    /**
     * @param int $line
     * @param string $message
     * @param string $sourceClass
     * @return \Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError
     */
    public function create($line, $message, $sourceClass, \ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $line = (int) $line;
        $message = (string) $message;
        $sourceClass = (string) $sourceClass;
        return new \Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError($line, $message, $sourceClass, $smartFileInfo);
    }
}
