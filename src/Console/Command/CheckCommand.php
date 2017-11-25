<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use PhpCsFixer\Differ\DiffConsoleFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Application\Application;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;
use Symplify\EasyCodingStandard\Console\Output\CheckCommandReporter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\Error\FileDiff;

final class CheckCommand extends Command
{
    /**
     * @var string
     */
    private const NAME = 'check';

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
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var CheckCommandReporter
     */
    private $checkCommandReporter;

    public function __construct(
        Application $application,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        Configuration $configuration,
        ErrorCollector $errorCollector,
        SymfonyStyle $symfonyStyle,
        CheckCommandReporter $checkCommandReporter
    ) {
        parent::__construct();

        $this->ecsApplication = $application;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->configuration = $configuration;
        $this->errorCollector = $errorCollector;
        $this->symfonyStyle = $symfonyStyle;
        $this->checkCommandReporter = $checkCommandReporter;
    }

    protected function configure(): void
    {
        $this->setName(self::NAME);
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

        foreach ($this->errorCollector->getFileDiffs() as $file => $fileDiffs) {
            $this->symfonyStyle->newLine(2);
            $this->symfonyStyle->writeln($file);

            /** @var FileDiff[] $fileDiffs */
            foreach ($fileDiffs as $fileDiff) {
                $diffFormatter = new DiffConsoleFormatter(true, sprintf(
                    '<comment>      ---------- begin diff ----------</comment>%s%%s%s<comment>      ----------- end diff -----------</comment>',
                    PHP_EOL,
                    PHP_EOL
                ));

                $output = PHP_EOL . $diffFormatter->format($fileDiff->getDiff()) . PHP_EOL;

                $this->symfonyStyle->writeln($output);

                $this->symfonyStyle->writeln('Applied checkers:');
                $this->symfonyStyle->newLine();
                $this->symfonyStyle->listing($fileDiff->getAppliedCheckers());
            }
        }

        if ($this->errorCollector->getErrorCount() === 0) {
            $this->symfonyStyle->newLine();
            $this->symfonyStyle->success('No errors found. Great job - your code is shiny in style!');
            $this->checkCommandReporter->reportUnusedSkipped();
            $this->reportPerformance();

            return 0;
        }

        $this->symfonyStyle->newLine();

        $exitCode = $this->configuration->isFixer() ? $this->printAfterFixerStatus() : $this->printNoFixerStatus();

        $this->reportPerformance();

        return $exitCode;
    }

    private function printAfterFixerStatus(): int
    {
        if ($this->configuration->showErrorTable()) {
            $this->easyCodingStandardStyle->printErrors($this->errorCollector->getUnfixableErrors());
        }

        if ($this->errorCollector->getUnfixableErrorCount() === 0) {
            $this->symfonyStyle->success(sprintf(
                '%d %s successfully fixed and no other found!',
                $this->errorCollector->getFixableErrorCount(),
                $this->errorCollector->getFixableErrorCount() === 1 ? 'error' : 'errors'
            ));

            return 0;
        }

        $this->printErrorMessageFromErrorCounts(
            $this->errorCollector->getUnfixableErrorCount(),
            $this->errorCollector->getFixableErrorCount()
        );

        return 1;
    }

    private function printNoFixerStatus(): int
    {
        if ($this->configuration->showErrorTable()) {
            $this->easyCodingStandardStyle->printErrors($this->errorCollector->getAllErrors());
        }

        $this->printErrorMessageFromErrorCounts(
            $this->errorCollector->getErrorCount(),
            $this->errorCollector->getFixableErrorCount()
        );

        return 1;
    }

    private function printErrorMessageFromErrorCounts(int $errorCount, int $fixableErrorCount): void
    {
        $this->symfonyStyle->error(sprintf(
            $errorCount === 1 ? 'Found %d error.' : 'Found %d errors.',
            $errorCount
        ));

        if (! $fixableErrorCount || $this->configuration->isFixer()) {
            return;
        }

        $this->symfonyStyle->success(sprintf(
            ' %s of them %s fixable! Just add "--fix" to console command and rerun to apply.',
            ($errorCount === $fixableErrorCount) ? 'ALL' : $fixableErrorCount,
            ($fixableErrorCount === 1) ? 'is' : 'are'
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

    private function reportPerformance(): void
    {
        if (! $this->configuration->showPerformance()) {
            return;
        }

        $this->checkCommandReporter->reportPerformance();
    }
}
