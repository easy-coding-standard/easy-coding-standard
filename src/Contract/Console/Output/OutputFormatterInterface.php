<?php

namespace Symplify\EasyCodingStandard\Contract\Console\Output;

use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
interface OutputFormatterInterface
{
    /**
     * @param \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult
     * @param int $processedFilesCount
     * @return int
     */
    public function report($errorAndDiffResult, $processedFilesCount);
    /**
     * @return string
     */
    public function getName();
}
