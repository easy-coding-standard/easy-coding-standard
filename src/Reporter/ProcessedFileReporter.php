<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\Reporter;

use ECSPrefix20220607\Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector;
use ECSPrefix20220607\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use ECSPrefix20220607\Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Configuration;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
final class ProcessedFileReporter
{
    /**
     * @var \Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector
     */
    private $outputFormatterCollector;
    public function __construct(OutputFormatterCollector $outputFormatterCollector)
    {
        $this->outputFormatterCollector = $outputFormatterCollector;
    }
    /**
     * @param array{system_errors?: SystemError[]|string[], file_diffs?: FileDiff[], coding_standard_errors?: CodingStandardError[], system_errors_count?: int} $errorsAndDiffs
     */
    public function report(array $errorsAndDiffs, Configuration $configuration) : int
    {
        $outputFormat = $configuration->getOutputFormat();
        $outputFormatter = $this->outputFormatterCollector->getByName($outputFormat);
        /** @var SystemError[]|string[] $systemErrors */
        $systemErrors = $errorsAndDiffs[Bridge::SYSTEM_ERRORS] ?? [];
        /** @var FileDiff[] $fileDiffs */
        $fileDiffs = $errorsAndDiffs[Bridge::FILE_DIFFS] ?? [];
        /** @var CodingStandardError[] $codingStandardErrors */
        $codingStandardErrors = $errorsAndDiffs[Bridge::CODING_STANDARD_ERRORS] ?? [];
        $errorAndDiffResult = new ErrorAndDiffResult($codingStandardErrors, $fileDiffs, $systemErrors);
        return $outputFormatter->report($errorAndDiffResult, $configuration);
    }
}
