<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\SmartFileSystem\SmartFileInfo;

interface FileProcessorInterface
{
    public function processFile(SmartFileInfo $smartFileInfo): string;

    /**
     * @return object[]
     */
    public function getCheckers(): array;
}
