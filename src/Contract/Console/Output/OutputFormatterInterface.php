<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\Contract\Console\Output;

use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Configuration;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
interface OutputFormatterInterface
{
    public function report(ErrorAndDiffResult $errorAndDiffResult, Configuration $configuration) : int;
    public function getName() : string;
}
