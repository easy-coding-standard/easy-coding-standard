<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Console\Output;

interface OutputFormatterInterface
{
    public function report(int $processedFilesCount): int;

    public function getName(): string;
}
