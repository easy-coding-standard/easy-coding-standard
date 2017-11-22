<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use SplFileInfo;

interface FileProcessorInterface
{
    public function processFile(SplFileInfo $file): void;

    public function processFileSecondRun(SplFileInfo $file): void;
}
