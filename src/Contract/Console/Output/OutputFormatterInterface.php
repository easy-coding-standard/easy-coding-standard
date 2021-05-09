<?php

namespace Symplify\EasyCodingStandard\Contract\Console\Output;

use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;

interface OutputFormatterInterface
{
    /**
     * @param int $processedFilesCount
     * @return int
     */
    public function report(ErrorAndDiffResult $errorAndDiffResult, $processedFilesCount);

    /**
     * @return string
     */
    public function getName();
}
