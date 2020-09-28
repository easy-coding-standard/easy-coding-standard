<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Nette\Utils\Json;
use Symplify\EasyCodingStandard\Configuration\Configuration;
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
    public const NAME = 'json';

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    public function __construct(Configuration $configuration, EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        $this->configuration = $configuration;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    public function report(ErrorAndDiffResult $errorAndDiffResult, int $processedFilesCount): int
    {
        $json = $this->createJsonContent($errorAndDiffResult);
        $this->easyCodingStandardStyle->writeln($json);

        $errorCount = $errorAndDiffResult->getErrorCount();
        return $errorCount === 0 ? ShellCode::SUCCESS : ShellCode::ERROR;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function createJsonContent(ErrorAndDiffResult $errorAndDiffResult): string
    {
        $errorsArray = $this->createBaseErrorsArray($errorAndDiffResult);

        $firstResolvedConfigFileInfo = $this->configuration->getFirstResolvedConfigFileInfo();
        if ($firstResolvedConfigFileInfo !== null) {
            $errorsArray['meta']['config'] = $firstResolvedConfigFileInfo->getRealPath();
        }

        foreach ($errorAndDiffResult->getErrors() as $codingStandardError) {
            $errorsArray['files'][$codingStandardError->getRelativeFilePathFromCwd()]['errors'][] = [
                'line' => $codingStandardError->getLine(),
                'message' => $codingStandardError->getMessage(),
                'source_class' => $codingStandardError->getCheckerClass(),
            ];
        }

        foreach ($errorAndDiffResult->getFileDiffs() as $fileDiff) {
            $errorsArray['files'][$fileDiff->getRelativeFilePathFromCwd()]['diffs'][] = [
                'diff' => $fileDiff->getDiff(),
                'applied_checkers' => $fileDiff->getAppliedCheckers(),
            ];
        }

        return Json::encode($errorsArray, Json::PRETTY);
    }

    /**
     * @return mixed[]
     */
    private function createBaseErrorsArray(ErrorAndDiffResult $errorAndDiffResult): array
    {
        return [
            'meta' => [
                'version' => $this->configuration->getPrettyVersion(),
            ],
            'totals' => [
                'errors' => $errorAndDiffResult->getErrorCount(),
                'diffs' => $errorAndDiffResult->getFileDiffsCount(),
            ],
            'files' => [],
        ];
    }
}
