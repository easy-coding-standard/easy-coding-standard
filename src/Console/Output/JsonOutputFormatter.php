<?php

namespace Symplify\EasyCodingStandard\Console\Output;

use ECSPrefix20210507\Nette\Utils\Json;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\PackageBuilder\Composer\PackageVersionProvider;
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
    /**
     * @param \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle
     */
    public function __construct($easyCodingStandardStyle)
    {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }
    /**
     * @param \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult
     * @param int $processedFilesCount
     * @return int
     */
    public function report($errorAndDiffResult, $processedFilesCount)
    {
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
     * @param \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult
     * @return string
     */
    public function createJsonContent($errorAndDiffResult)
    {
        $errorsArray = $this->createBaseErrorsArray($errorAndDiffResult);
        $codingStandardErrors = $errorAndDiffResult->getErrors();
        foreach ($codingStandardErrors as $codingStandardError) {
            $errorsArray[self::FILES][$codingStandardError->getRelativeFilePathFromCwd()]['errors'][] = ['line' => $codingStandardError->getLine(), 'file_path' => $codingStandardError->getRelativeFilePathFromCwd(), 'message' => $codingStandardError->getMessage(), 'source_class' => $codingStandardError->getCheckerClass()];
        }
        $fileDiffs = $errorAndDiffResult->getFileDiffs();
        foreach ($fileDiffs as $fileDiff) {
            $errorsArray[self::FILES][$fileDiff->getRelativeFilePathFromCwd()]['diffs'][] = ['diff' => $fileDiff->getDiff(), 'applied_checkers' => $fileDiff->getAppliedCheckers()];
        }
        return Json::encode($errorsArray, Json::PRETTY);
    }
    /**
     * @return mixed[]
     * @param \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult
     */
    private function createBaseErrorsArray($errorAndDiffResult)
    {
        $packageVersionProvider = new PackageVersionProvider();
        $version = $packageVersionProvider->provide('symplify/easy-coding-standard');
        return ['meta' => ['version' => $version], 'totals' => ['errors' => $errorAndDiffResult->getErrorCount(), 'diffs' => $errorAndDiffResult->getFileDiffsCount()], self::FILES => []];
    }
}
