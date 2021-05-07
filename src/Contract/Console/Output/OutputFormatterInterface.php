<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Console\Output;

use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;

interface OutputFormatterInterface
{
    public function report(ErrorAndDiffResult $errorAndDiffResult, int $processedFilesCount): int;

    public function getName(): string;
}
