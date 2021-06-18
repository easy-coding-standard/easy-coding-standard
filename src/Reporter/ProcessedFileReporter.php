<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Reporter;

use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
final class ProcessedFileReporter
{
    /**
     * @var \Symplify\EasyCodingStandard\Configuration\Configuration
     */
    private $configuration;
    /**
     * @var \Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector
     */
    private $outputFormatterCollector;
    public function __construct(\Symplify\EasyCodingStandard\Configuration\Configuration $configuration, \Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector $outputFormatterCollector)
    {
        $this->configuration = $configuration;
        $this->outputFormatterCollector = $outputFormatterCollector;
    }
    /**
     * @param array<SystemError|FileDiff|CodingStandardError> $errorsAndDiffs
     */
    public function report(array $errorsAndDiffs) : int
    {
        $outputFormat = $this->configuration->getOutputFormat();
        $outputFormatter = $this->outputFormatterCollector->getByName($outputFormat);
        /** @var SystemError[] $systemErrors */
        $systemErrors = \array_filter($errorsAndDiffs, function (object $object) {
            return $object instanceof \Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
        });
        /** @var FileDiff[] $fileDiffs */
        $fileDiffs = \array_filter($errorsAndDiffs, function (object $object) {
            return $object instanceof \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
        });
        /** @var CodingStandardError[] $codingStandardErrors */
        $codingStandardErrors = \array_filter($errorsAndDiffs, function (object $object) {
            return $object instanceof \Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
        });
        $errorAndDiffResult = new \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult($codingStandardErrors, $fileDiffs, $systemErrors);
        return $outputFormatter->report($errorAndDiffResult);
    }
}
