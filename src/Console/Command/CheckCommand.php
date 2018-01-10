<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\Application;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;
use Symplify\EasyCodingStandard\Console\Output\CheckCommandReporter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Error\FileDiff;

final class CheckCommand extends Command
{
    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var Application
     */
    private $ecsApplication;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var CheckCommandReporter
     */
    private $checkCommandReporter;

    public function __construct(
        Application $application,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        Configuration $configuration,
        ErrorAndDiffCollector $errorAndDiffCollector,
        CheckCommandReporter $checkCommandReporter
    ) {
        parent::__construct();

        $this->ecsApplication = $application;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->configuration = $configuration;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->checkCommandReporter = $checkCommandReporter;
    }

    protected function configure(): void
    {
        $this->setName('check');
        $this->setDescription('Check coding standard in one or more directories.');
        $this->addArgument('source', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The path(s) to be checked.');
        $this->addOption('fix', null, null, 'Fix found violations.');
        $this->addOption('clear-cache', null, null, 'Clear cache for already checked files.');
        $this->addOption(
            'show-performance',
            null,
            InputOption::VALUE_NONE,
            'Show performance of every checker.'
        );
        $this->addOption(
            'no-progress-bar',
            null,
            InputOption::VALUE_NONE,
            'Hide progress bar. Useful e.g. for nicer CI output.'
        );
        $this->addOption(
            'no-error-table',
            null,
            InputOption::VALUE_NONE,
            'Hide error table. Useful e.g. for fast check of error count.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ensureSomeCheckersAreRegistered();

        $this->configuration->resolveFromInput($input);
        $this->ecsApplication->run();

        $this->checkCommandReporter->reportFileDiffs($this->errorAndDiffCollector->getFileDiffs());

        if ($this->errorAndDiffCollector->getErrorCount() === 0
            && $this->errorAndDiffCollector->getFileDiffsCount() === 0
        ) {
            $this->easyCodingStandardStyle->newLine();
            $this->easyCodingStandardStyle->success('No errors found. Great job - your code is shiny in style!');
            $this->checkCommandReporter->reportUnusedSkipped();
            $this->checkCommandReporter->reportPerformance();

            return 0;
        }

        $this->easyCodingStandardStyle->newLine();

        $exitCode = $this->configuration->isFixer() ? $this->printAfterFixerStatus() : $this->printNoFixerStatus();

        $this->checkCommandReporter->reportPerformance();

        return $exitCode;
    }

    private function printAfterFixerStatus(): int
    {
        if ($this->configuration->showErrorTable()) {
            $this->easyCodingStandardStyle->printErrors($this->errorAndDiffCollector->getErrors());
        }

        if ($this->errorAndDiffCollector->getErrorCount() === 0) {
            $this->easyCodingStandardStyle->success(sprintf(
                '%d error%s successfully fixed and no other found!',
                $this->errorAndDiffCollector->getFileDiffsCount(),
                $this->errorAndDiffCollector->getFileDiffsCount() === 1 ? '' : 's'
            ));

            return 0;
        }

        $this->printErrorMessageFromErrorCounts(
            $this->errorAndDiffCollector->getErrorCount(),
            $this->errorAndDiffCollector->getFileDiffsCount()
        );

        return 1;
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

        return 1;
    }

    private function printErrorMessageFromErrorCounts(int $errorCount, int $fileDiffsCount): void
    {
        if ($errorCount) {
            $this->easyCodingStandardStyle->error(sprintf(
                'Found %d error%s.',
                $errorCount,
                $errorCount === 1 ? '' : 's'
            ));
        }

        if (! $fileDiffsCount || $this->configuration->isFixer()) {
            return;
        }

        $this->easyCodingStandardStyle->success(sprintf(
            '%d file%s %s fixable! Just add "--fix" to console command and rerun to apply.',
            $fileDiffsCount,
            $fileDiffsCount === 1 ? '' : 's',
            $fileDiffsCount === 1 ? 'is' : 'are'
        ));
    }

    private function ensureSomeCheckersAreRegistered(): void
    {
        $totalCheckersLoaded = $this->ecsApplication->getCheckerCount();
        if ($totalCheckersLoaded === 0) {
            throw new NoCheckersLoadedException(
                'No checkers were found. Registers them in your config in "checkers:" '
                . 'section, load them via "--config <file>.neon" or "--level <level> option.'
            );
        }
    }
}
