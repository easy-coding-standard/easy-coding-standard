<?php

namespace Symplify\EasyCodingStandard\Console\Output;

use ECSPrefix20210509\Nette\Utils\Json;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\PackageBuilder\Composer\PackageVersionProvider;
use Symplify\PackageBuilder\Console\ShellCode;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Console\Output\JsonOutputFormatterTest
 */
final class JsonOutputFormatter implements \Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface
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
    public function __construct(\Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }
    /**
     * @param int $processedFilesCount
     * @return int
     */
    public function report(\Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult, $processedFilesCount)
    {
        $processedFilesCount = (int) $processedFilesCount;
        $json = $this->createJsonContent($errorAndDiffResult);
        $this->easyCodingStandardStyle->writeln($json);
        $errorCount = $errorAndDiffResult->getErrorCount();
        return $errorCount === 0 ? \Symplify\PackageBuilder\Console\ShellCode::SUCCESS : \Symplify\PackageBuilder\Console\ShellCode::ERROR;
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
    public function createJsonContent(\Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult)
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
        return \ECSPrefix20210509\Nette\Utils\Json::encode($errorsArray, \ECSPrefix20210509\Nette\Utils\Json::PRETTY);
    }
    /**
     * @return mixed[]
     */
    private function createBaseErrorsArray(\Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult)
    {
        $packageVersionProvider = new \Symplify\PackageBuilder\Composer\PackageVersionProvider();
        $version = $packageVersionProvider->provide('symplify/easy-coding-standard');
        return ['meta' => ['version' => $version], 'totals' => ['errors' => $errorAndDiffResult->getErrorCount(), 'diffs' => $errorAndDiffResult->getFileDiffsCount()], self::FILES => []];
    }
}
