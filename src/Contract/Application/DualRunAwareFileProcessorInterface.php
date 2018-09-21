<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

interface DualRunAwareFileProcessorInterface extends FileProcessorInterface
{
    /**
     * @return mixed[]
     */
    public function getDualRunCheckers(): array;

    public function processFileSecondRun(SmartFileInfo $smartFileInfo): string;
}
