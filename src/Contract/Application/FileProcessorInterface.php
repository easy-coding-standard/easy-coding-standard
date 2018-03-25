<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symfony\Component\Finder\SplFileInfo;

interface FileProcessorInterface
{
    public function processFile(SplFileInfo $fileInfo): string;

    /**
     * @return mixed[]
     */
    public function getCheckers(): array;
}
