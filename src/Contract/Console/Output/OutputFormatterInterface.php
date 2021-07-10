<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Contract\Console\Output;

use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
interface OutputFormatterInterface
{
    /**
     * @param \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult
     * @param \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration
     */
    public function report($errorAndDiffResult, $configuration) : int;
    public function getName() : string;
}
