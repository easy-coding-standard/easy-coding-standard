<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Error\FileDiff;
use Symplify\PackageBuilder\Console\ShellCode;

final class TableOutputFormatter implements OutputFormatterInterface
{
    /**
     * @var string
     */
    public const NAME = 'table';

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        Configuration $configuration,
        ErrorAndDiffCollector $errorAndDiffCollector
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->configuration = $configuration;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
    }

    public function report(int $processedFilesCount): int
    {
        $this->reportFileDiffs($this->errorAndDiffCollector->getFileDiffs());

        if ($this->errorAndDiffCollector->getErrorCount() === 0
            && $this->errorAndDiffCollector->getFileDiffsCount() === 0
        ) {
            if ($processedFilesCount) {
                $this->easyCodingStandardStyle->newLine();
            }

            $this->easyCodingStandardStyle->success('No errors found. Great job - your code is shiny in style!');

            return ShellCode::SUCCESS;
        }

        $this->easyCodingStandardStyle->newLine();

        return $this->configuration->isFixer() ? $this->printAfterFixerStatus() : $this->printNoFixerStatus();
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param FileDiff[][] $fileDiffPerFile
     */
    private function reportFileDiffs(array $fileDiffPerFile): void
    {
        if (! count($fileDiffPerFile)) {
            return;
        }

        $this->easyCodingStandardStyle->newLine();

        $i = 0;
        foreach ($fileDiffPerFile as $file => $fileDiffs) {
            $this->easyCodingStandardStyle->newLine(2);
            $this->easyCodingStandardStyle->writeln(sprintf('<options=bold>%d) %s</>', ++$i, $file));

            foreach ($fileDiffs as $fileDiff) {
                $this->easyCodingStandardStyle->newLine();
                $this->easyCodingStandardStyle->writeln($fileDiff->getDiffConsoleFormatted());
                $this->easyCodingStandardStyle->newLine();

                $this->easyCodingStandardStyle->writeln('Applied checkers:');
                $this->easyCodingStandardStyle->newLine();
                $this->easyCodingStandardStyle->listing($fileDiff->getAppliedCheckers());
            }
        }
    }

    private function printAfterFixerStatus(): int
    {
        if ($this->configuration->showErrorTable()) {
            $this->easyCodingStandardStyle->printErrors($this->errorAndDiffCollector->getErrors());
        }

        if ($this->errorAndDiffCollector->getErrorCount() === 0) {
            $this->easyCodingStandardStyle->success(
                sprintf(
                    '%d error%s successfully fixed and no other found!',
                    $this->errorAndDiffCollector->getFileDiffsCount(),
                    $this->errorAndDiffCollector->getFileDiffsCount() === 1 ? '' : 's'
                )
            );

            return ShellCode::SUCCESS;
        }

        $this->printErrorMessageFromErrorCounts(
            $this->errorAndDiffCollector->getErrorCount(),
            $this->errorAndDiffCollector->getFileDiffsCount()
        );

        return ShellCode::ERROR;
    }

    private function printNoFixerStatus(): int
    {
        if ($this->configuration->showErrorTable()) {
            $errors = $this->errorAndDiffCollector->getErrors();
            if (count($errors)) {
                $this->easyCodingStandardStyle->newLine();
                $this->easyCodingStandardStyle->printErrors($errors);
            }
        }

        $this->printErrorMessageFromErrorCounts(
            $this->errorAndDiffCollector->getErrorCount(),
            $this->errorAndDiffCollector->getFileDiffsCount()
        );

        return ShellCode::ERROR;
    }

    private function printErrorMessageFromErrorCounts(int $errorCount, int $fileDiffsCount): void
    {
        if ($errorCount) {
            $this->easyCodingStandardStyle->error(
                sprintf(
                    'Found %d error%s that need%s to be fixed manually.',
                    $errorCount,
                    $errorCount === 1 ? '' : 's',
                    $errorCount === 1 ? '' : 's'
                )
            );
        }

        if (! $fileDiffsCount || $this->configuration->isFixer()) {
            return;
        }

        $this->easyCodingStandardStyle->fixableError(
            sprintf(
                '%s%d %s fixable! Just add "--fix" to console command and rerun to apply.',
                $errorCount ? 'Good news is that ' : '',
                $fileDiffsCount,
                $fileDiffsCount === 1 ? 'file is' : 'files are'
            )
        );
    }
}
