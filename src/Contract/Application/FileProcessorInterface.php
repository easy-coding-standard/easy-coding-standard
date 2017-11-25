<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use SplFileInfo;

interface FileProcessorInterface
{
    public function processFile(\Symfony\Component\Finder\SplFileInfo $file): void;

    public function processFileSecondRun(\Symfony\Component\Finder\SplFileInfo $file): void;

    /**
     * @return mixed[]
     */
    public function getCheckers(): array;

    /**
     * @return mixed[]
     */
    public function getDualRunCheckers(): array;
}
