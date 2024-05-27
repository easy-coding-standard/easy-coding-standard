<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Nette\Utils\Json;
use Symplify\EasyCodingStandard\Console\ExitCode;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;

/**
 * @see \Symplify\EasyCodingStandard\Tests\Console\Output\JsonOutputFormatterTest
 */
final readonly class JsonOutputFormatter implements OutputFormatterInterface
{
    /**
     * @var string
     */
    private const FILES = 'files';

    public function __construct(
        private EasyCodingStandardStyle $easyCodingStandardStyle,
        private ExitCodeResolver $exitCodeResolver
    ) {
    }

    /**
     * @return ExitCode::*
     */
    public function report(ErrorAndDiffResult $errorAndDiffResult, Configuration $configuration): int
    {
        $json = $this->createJsonContent($errorAndDiffResult);
        $this->easyCodingStandardStyle->writeln($json);

        return $this->exitCodeResolver->resolve($errorAndDiffResult, $configuration);
    }

    public static function getName(): string
    {
        return 'json';
    }

    public static function hasSupportForProgressBars(): bool
    {
        return false;
    }

    /**
     * @api
     */
    public function createJsonContent(ErrorAndDiffResult $errorAndDiffResult): string
    {
        $errorsArrayJson = $this->createBaseErrorsJson($errorAndDiffResult);

        $codingStandardErrors = $errorAndDiffResult->getErrors();
        foreach ($codingStandardErrors as $codingStandardError) {
            $errorsArrayJson[self::FILES][$codingStandardError->getRelativeFilePath()]['errors'][] = [
                'line' => $codingStandardError->getLine(),
                'file_path' => $codingStandardError->getRelativeFilePath(),
                'message' => $codingStandardError->getMessage(),
                'source_class' => $codingStandardError->getCheckerClass(),
            ];
        }

        $fileDiffs = $errorAndDiffResult->getFileDiffs();
        foreach ($fileDiffs as $fileDiff) {
            $errorsArrayJson[self::FILES][$fileDiff->getRelativeFilePath()]['diffs'][] = [
                'diff' => $fileDiff->getDiff(),
                'applied_checkers' => $fileDiff->getAppliedCheckers(),
            ];
        }

        return Json::encode($errorsArrayJson, Json::PRETTY);
    }

    /**
     * @return array{totals: array{errors: int, diffs: int}, files: string[]}
     */
    private function createBaseErrorsJson(ErrorAndDiffResult $errorAndDiffResult): array
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
