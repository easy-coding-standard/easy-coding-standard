<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

final class ErrorFactory
{
    public function createFromLineMessageSourceClass(int $line, string $message, string $sourceClass): Error
    {
        return new Error($line, $message, $sourceClass);
    }
}
