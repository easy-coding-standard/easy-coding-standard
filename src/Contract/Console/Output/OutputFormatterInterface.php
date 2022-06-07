<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Contract\Console\Output;

use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
interface OutputFormatterInterface
{
    public function report(ErrorAndDiffResult $errorAndDiffResult, Configuration $configuration) : int;
    public function getName() : string;
}
