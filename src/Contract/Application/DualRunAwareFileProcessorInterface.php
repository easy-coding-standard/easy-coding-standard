<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\SmartFileSystem\SmartFileInfo;

interface DualRunAwareFileProcessorInterface extends FileProcessorInterface
{
    /**
     * @return mixed[]
     */
    public function getDualRunCheckers(): array;

    public function processFileSecondRun(SmartFileInfo $smartFileInfo): string;
}
