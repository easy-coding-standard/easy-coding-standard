<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Error;

use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use ECSPrefix20210604\Symplify\SmartFileSystem\SmartFileInfo;
final class ErrorFactory
{
    public function create(int $line, string $message, string $sourceClass, \ECSPrefix20210604\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : \Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError
    {
        return new \Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError($line, $message, $sourceClass, $smartFileInfo);
    }
}
