<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Error;

use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
final class ErrorAndDiffResultFactory
{
    /**
     * @var \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;
    public function __construct(\Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector $errorAndDiffCollector)
    {
        $this->errorAndDiffCollector = $errorAndDiffCollector;
    }
    public function create() : \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult
    {
        return new \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult($this->errorAndDiffCollector->getErrors(), $this->errorAndDiffCollector->getFileDiffs(), $this->errorAndDiffCollector->getSystemErrors());
    }
}
