<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;

final class ErrorAndDiffResultFactory
{
    public function create(ErrorAndDiffCollector $errorAndDiffCollector): ErrorAndDiffResult
    {
        return new ErrorAndDiffResult(
            $errorAndDiffCollector->getErrors(),
            $errorAndDiffCollector->getFileDiffs(),
            $errorAndDiffCollector->getSystemErrors()
        );
    }
}
