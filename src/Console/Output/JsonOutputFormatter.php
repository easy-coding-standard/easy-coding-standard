<?php

namespace Symplify\EasyCodingStandard\Console\Output;

use Nette\Utils\Json;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\PackageBuilder\Console\ShellCode;

/**
 * @see \Symplify\EasyCodingStandard\Tests\Console\Output\JsonOutputFormatterTest
 */
final class JsonOutputFormatter implements OutputFormatterInterface
{
    /**
     * @var string
     */
    const NAME = 'json';

    /**
     * @var string
     */
    const FILES = 'files';

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    public function __construct(EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    /**
     * @param int $processedFilesCount
     * @return int
     */
    public function report(ErrorAndDiffResult $errorAndDiffResult, $processedFilesCount)
    {
        $processedFilesCount = (int) $processedFilesCount;
        $json = $this->createJsonContent($errorAndDiffResult);
        $this->easyCodingStandardStyle->writeln($json);

        $errorCount = $errorAndDiffResult->getErrorCount();
        return $errorCount === 0 ? ShellCode::SUCCESS : ShellCode::ERROR;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public function createJsonContent(ErrorAndDiffResult $errorAndDiffResult)
    {
        $errorsArray = $this->createBaseErrorsArray($errorAndDiffResult);

        $codingStandardErrors = $errorAndDiffResult->getErrors();
        foreach ($codingStandardErrors as $codingStandardError) {
            $errorsArray[self::FILES][$codingStandardError->getRelativeFilePathFromCwd()]['errors'][] = [
                'line' => $codingStandardError->getLine(),
                'file_path' => $codingStandardError->getRelativeFilePathFromCwd(),
                'message' => $codingStandardError->getMessage(),
                'source_class' => $codingStandardError->getCheckerClass(),
            ];
        }

        $fileDiffs = $errorAndDiffResult->getFileDiffs();
        foreach ($fileDiffs as $fileDiff) {
            $errorsArray[self::FILES][$fileDiff->getRelativeFilePathFromCwd()]['diffs'][] = [
                'diff' => $fileDiff->getDiff(),
                'applied_checkers' => $fileDiff->getAppliedCheckers(),
            ];
        }

        return Json::encode($errorsArray, Json::PRETTY);
    }

    /**
     * @return mixed[]
     */
    private function createBaseErrorsArray(ErrorAndDiffResult $errorAndDiffResult)
    {
        return [
            'totals' => [
                'errors' => $errorAndDiffResult->getErrorCount(),
                'diffs' => $errorAndDiffResult->getFileDiffsCount(),
            ],
            self::FILES => [],
        ];
    }
}
