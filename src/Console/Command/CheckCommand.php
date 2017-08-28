<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Application\Application;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Performance\CheckerMetricRecorder;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

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
    private $applicationRunner;

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ErrorCollector
     */
    private $errorDataCollector;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var CheckerMetricRecorder
     */
    private $checkerMetricRecorder;

    public function __construct(
        Application $application,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        Skipper $skipper,
        Configuration $configuration,
        ErrorCollector $errorDataCollector,
        SymfonyStyle $symfonyStyle,
        FixerFileProcessor $fixerFileProcessor,
        SniffFileProcessor $sniffFileProcessor,
        CheckerMetricRecorder $checkerMetricRecorder
    ) {
        parent::__construct();

        $this->applicationRunner = $application;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
        $this->errorDataCollector = $errorDataCollector;
        $this->symfonyStyle = $symfonyStyle;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->checkerMetricRecorder = $checkerMetricRecorder;
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ensureSomeCheckersAreRegistered();

        $this->configuration->resolveFromInput($input);
        $this->applicationRunner->run();

        if ($this->errorDataCollector->getErrorCount() === 0) {
            $this->symfonyStyle->newLine();
            $this->symfonyStyle->success('No errors found. Great job - your code is shiny in style!');
            $this->reportUnusedSkipped();
            $this->reportPerformance();

            return 0;
        }

        $this->symfonyStyle->newLine();
        $this->reportPerformance();

        return $this->configuration->isFixer() ? $this->printAfterFixerStatus() : $this->printNoFixerStatus();
    }

    private function printAfterFixerStatus(): int
    {
        $this->easyCodingStandardStyle->printErrors($this->errorDataCollector->getUnfixableErrors());

        if ($this->errorDataCollector->getUnfixableErrorCount() === 0) {
            $this->symfonyStyle->success(sprintf(
                '%d %s successfully fixed and no other found!',
                $this->errorDataCollector->getFixableErrorCount(),
                $this->errorDataCollector->getFixableErrorCount() === 1 ? 'error' : 'errors'
            ));

            return 0;
        }

        $this->printErrorMessageFromErrorCounts(
            $this->errorDataCollector->getUnfixableErrorCount(),
            $this->errorDataCollector->getFixableErrorCount()
        );

        return 1;
    }

    private function printNoFixerStatus(): int
    {
        $this->easyCodingStandardStyle->printErrors($this->errorDataCollector->getAllErrors());

        $this->printErrorMessageFromErrorCounts(
            $this->errorDataCollector->getErrorCount(),
            $this->errorDataCollector->getFixableErrorCount()
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

    private function reportUnusedSkipped(): void
    {
        foreach ($this->skipper->getUnusedSkipped() as $skippedClass => $skippedFiles) {
            foreach ($skippedFiles as $skippedFile) {
                $this->symfonyStyle->error(sprintf(
                    'Skipped checker "%s" and file path "%s" were not found. '
                        . 'You can remove them from "parameters: > skip:" section in your config.',
                    $skippedClass,
                    $skippedFile
                ));
            }
        }
    }

    private function ensureSomeCheckersAreRegistered(): void
    {
        $totalCheckersLoaded = count($this->sniffFileProcessor->getSniffs())
            + count($this->fixerFileProcessor->getFixers());

        if ($totalCheckersLoaded === 0) {
            throw new NoCheckersLoadedException(
                'No checkers were found. Registers them in your config in "checkers:" '
                . 'section or load them via "--config <file>.neon" option.'
            );
        }
    }

    private function reportPerformance(): void
    {
        if (! $this->configuration->showPerformance()) {
            return;
        }

        $this->symfonyStyle->newLine();

        $this->symfonyStyle->title('Performance Statistics');

        $metrics = $this->checkerMetricRecorder->getMetrics();
        $metricsForTable = $this->prepareForTable($metrics);
        $this->symfonyStyle->table(['Checker', 'Total duration [ms]'], $metricsForTable);
    }

    /**
     * @param mixed[] $metrics
     * @return mixed[]
     */
    private function prepareForTable(array $metrics): array
    {
        $metricsForTable = [];
        foreach ($metrics as $checkerClass => $duration) {
            $metricsForTable[] = [$checkerClass, $duration];
        }

        return $metricsForTable;
    }
}
