<?php

namespace Symplify\EasyCodingStandard\Error;

use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
final class ErrorAndDiffResultFactory
{
    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;
    public function __construct(\Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector $errorAndDiffCollector)
    {
        $this->errorAndDiffCollector = $errorAndDiffCollector;
    }
    /**
     * @return \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult
     */
    public function create()
    {
        return new \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult($this->errorAndDiffCollector->getErrors(), $this->errorAndDiffCollector->getFileDiffs(), $this->errorAndDiffCollector->getSystemErrors());
    }
}
