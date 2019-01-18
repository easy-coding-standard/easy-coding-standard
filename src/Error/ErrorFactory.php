<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class ErrorFactory
{
    public function create(int $line, string $message, string $sourceClass, SmartFileInfo $smartFileInfo): Error
    {
        return new Error($line, $message, $sourceClass, $smartFileInfo);
    }
}
