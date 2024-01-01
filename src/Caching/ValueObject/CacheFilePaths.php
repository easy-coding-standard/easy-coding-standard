<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching\ValueObject;

final readonly class CacheFilePaths
{
    public function __construct(
        private string $firstDirectory,
        private string $secondDirectory,
        private string $filePath
    ) {
    }

    public function getFirstDirectory(): string
    {
        return $this->firstDirectory;
    }

    public function getSecondDirectory(): string
    {
        return $this->secondDirectory;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
