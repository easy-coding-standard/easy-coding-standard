<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symfony\Component\Finder\SplFileInfo;

interface FileProcessorInterface
{
    public function processFile(SplFileInfo $file): string;

    public function processFileSecondRun(SplFileInfo $file): string;

    /**
     * @return mixed[]
     */
    public function getCheckers(): array;

    /**
     * @return mixed[]
     */
    public function getDualRunCheckers(): array;
}
