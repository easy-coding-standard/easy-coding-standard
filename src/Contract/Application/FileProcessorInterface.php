<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

interface FileProcessorInterface
{
    public function processFile(SmartFileInfo $smartFileInfo): string;

    /**
     * @return mixed[]
     */
    public function getCheckers(): array;
}
