<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symfony\Component\Finder\SplFileInfo;

interface DualRunAwareFileProcessorInterface extends FileProcessorInterface
{
    /**
     * @return mixed[]
     */
    public function getDualRunCheckers(): array;

    public function processFileSecondRun(SplFileInfo $fileInfo): string;
}
