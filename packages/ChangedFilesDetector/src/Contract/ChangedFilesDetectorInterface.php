<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Contract;

interface ChangedFilesDetectorInterface
{
    public function addFile(string $filePath): void;

    public function invalidateFile(string $filePath): void;

    public function hasFileChanged(string $filePath): bool;

    public function clearCache(): void;
}
