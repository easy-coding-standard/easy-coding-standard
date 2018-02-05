<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

interface FileProcessorCollectorInterface
{
    public function addFileProcessor(FileProcessorInterface $fileProcessor): void;
}
