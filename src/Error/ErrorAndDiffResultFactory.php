<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;

final class ErrorAndDiffResultFactory
{
    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    public function __construct(ErrorAndDiffCollector $errorAndDiffCollector)
    {
        $this->errorAndDiffCollector = $errorAndDiffCollector;
    }

    public function create(): ErrorAndDiffResult
    {
        return new ErrorAndDiffResult(
            $this->errorAndDiffCollector->getErrors(),
            $this->errorAndDiffCollector->getFileDiffs(),
            $this->errorAndDiffCollector->getSystemErrors()
        );
    }
}
