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
     * @return \Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError
     */
    public function create(
        $line,
        $message,
        $sourceClass,
        SmartFileInfo $smartFileInfo
    ) {
        $line = (int) $line;
        $message = (string) $message;
        $sourceClass = (string) $sourceClass;
        return new CodingStandardError($line, $message, $sourceClass, $smartFileInfo);
    }
}
