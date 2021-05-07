<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ErrorFactory
{
    public function create(
        int $line,
        string $message,
        string $sourceClass,
        SmartFileInfo $smartFileInfo
    ): CodingStandardError {
        return new CodingStandardError($line, $message, $sourceClass, $smartFileInfo);
    }
}
