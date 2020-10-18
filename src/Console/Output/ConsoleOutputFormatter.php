<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\PackageBuilder\Console\ShellCode;

final class ConsoleOutputFormatter implements OutputFormatterInterface
{
    /**
     * @var string
     */
    public const NAME = 'console';

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(EasyCodingStandardStyle $easyCodingStandardStyle, Configuration $configuration)
    {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->configuration = $configuration;
    }

    public function report(ErrorAndDiffResult $errorAndDiffResult, int $processedFilesCount): int
    {
        $this->reportFileDiffs($errorAndDiffResult->getFileDiffs());

        if ($errorAndDiffResult->getErrorCount() === 0 && $errorAndDiffResult->getFileDiffsCount() === 0) {
            if ($processedFilesCount !== 0) {
                $this->easyCodingStandardStyle->newLine();
            }

            $this->easyCodingStandardStyle->success('No errors found. Great job - your code is shiny in style!');

            return ShellCode::SUCCESS;
        }

        $this->easyCodingStandardStyle->newLine();

        return $this->configuration->isFixer()
            ? $this->printAfterFixerStatus($errorAndDiffResult)
            : $this->printNoFixerStatus($errorAndDiffResult);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param FileDiff[] $fileDiffs
     */
    private function reportFileDiffs(array $fileDiffs): void
    {
        if (count($fileDiffs) === 0) {
            return;
        }

        $this->easyCodingStandardStyle->newLine(1);

        $i = 1;
        foreach ($fileDiffs as $fileDiff) {
            $this->easyCodingStandardStyle->newLine(2);

            $boldNumberedMessage = sprintf('<options=bold>%d) %s</>', $i, $fileDiff->getRelativeFilePathFromCwd());
            $this->easyCodingStandardStyle->writeln($boldNumberedMessage);

            ++$i;

            $this->easyCodingStandardStyle->newLine();
            $this->easyCodingStandardStyle->writeln($fileDiff->getDiffConsoleFormatted());
            $this->easyCodingStandardStyle->newLine();

            $this->easyCodingStandardStyle->writeln('Applied checkers:');
            $this->easyCodingStandardStyle->newLine();
            $this->easyCodingStandardStyle->listing($fileDiff->getAppliedCheckers());
        }
    }

    private function printAfterFixerStatus(ErrorAndDiffResult $errorAndDiffResult): int
    {
        if ($this->configuration->shouldShowErrorTable()) {
            $this->easyCodingStandardStyle->printErrors($errorAndDiffResult->getErrors());
        }

        if ($errorAndDiffResult->getErrorCount() === 0) {
            $successMessage = sprintf(
                '%d error%s successfully fixed and no other errors found!',
                $errorAndDiffResult->getFileDiffsCount(),
                $errorAndDiffResult->getFileDiffsCount() === 1 ? '' : 's'
            );
            $this->easyCodingStandardStyle->success($successMessage);

            return ShellCode::SUCCESS;
        }

        $this->printErrorMessageFromErrorCounts(
            $errorAndDiffResult->getErrorCount(),
            $errorAndDiffResult->getFileDiffsCount()
        );

        return ShellCode::ERROR;
    }

    private function printNoFixerStatus(ErrorAndDiffResult $errorAndDiffResult): int
    {
        if ($this->configuration->shouldShowErrorTable()) {
            $errors = $errorAndDiffResult->getErrors();
            if (count($errors) > 0) {
                $this->easyCodingStandardStyle->newLine();
                $this->easyCodingStandardStyle->printErrors($errors);
            }
        }

        $systemErrors = $errorAndDiffResult->getSystemErrors();
        foreach ($systemErrors as $systemError) {
            $this->easyCodingStandardStyle->newLine();
            $this->easyCodingStandardStyle->writeln($systemError->getFileWithLine());
            $this->easyCodingStandardStyle->warning($systemError->getMessage());
        }

        $this->printErrorMessageFromErrorCounts(
            $errorAndDiffResult->getErrorCount(),
            $errorAndDiffResult->getFileDiffsCount()
        );

        return ShellCode::ERROR;
    }

    private function printErrorMessageFromErrorCounts(int $errorCount, int $fileDiffsCount): void
    {
        if ($errorCount !== 0) {
            $errorMessage = sprintf(
                'Found %d error%s that need%s to be fixed manually.',
                $errorCount,
                $errorCount === 1 ? '' : 's',
                $errorCount === 1 ? 's' : ''
            );
            $this->easyCodingStandardStyle->error($errorMessage);
        }

        if (! $fileDiffsCount || $this->configuration->isFixer()) {
            return;
        }

        $fixableMessage = sprintf(
            '%s%d %s fixable! Just add "--fix" to console command and rerun to apply.',
            $errorCount !== 0 ? 'Good news is that ' : '',
            $fileDiffsCount,
            $fileDiffsCount === 1 ? 'error is' : 'errors are'
        );
        $this->easyCodingStandardStyle->warning($fixableMessage);
    }
}
