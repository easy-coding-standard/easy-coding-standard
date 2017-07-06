<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\Application;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\Skipper;

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

    public function __construct(
        Application $application,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        Skipper $skipper,
        Configuration $configuration,
        ErrorCollector $errorDataCollector
    ) {
        parent::__construct();

        $this->applicationRunner = $application;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
        $this->errorDataCollector = $errorDataCollector;
    }

    protected function configure(): void
    {
        $this->setName(self::NAME);
        $this->setDescription('Check coding standard in one or more directories.');
        $this->addArgument('source', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The path(s) to be checked.');
        $this->addOption('fix', null, null, 'Fix found violations.');
        $this->addOption('clear-cache', null, null, 'Clear cache for already checked files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration->resolveFromInput($input);
        $this->applicationRunner->run();

        if ($this->errorDataCollector->getErrorCount() === 0) {
            $this->easyCodingStandardStyle->newLine();
            $this->easyCodingStandardStyle->success('No errors found. Great job - your code is shiny in style!');
            $this->reportUnusedSkipped();

            return 0;
        }

        $this->easyCodingStandardStyle->newLine();

        return $this->configuration->isFixer() ? $this->printAfterFixerStatus() : $this->printNoFixerStatus();
    }

    private function printAfterFixerStatus(): int
    {
        $this->easyCodingStandardStyle->printErrors($this->errorDataCollector->getUnfixableErrors());

        if ($this->errorDataCollector->getUnfixableErrorCount() === 0) {
            $this->easyCodingStandardStyle->success(sprintf(
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

        return 0;
    }

    private function printErrorMessageFromErrorCounts(int $errorCount, int $fixableErrorCount): void
    {
        $this->easyCodingStandardStyle->error(sprintf(
            $errorCount === 1 ? 'Found %d error.' : 'Found %d errors.',
            $errorCount
        ));

        if (! $fixableErrorCount || $this->configuration->isFixer()) {
            return;
        }

        $this->easyCodingStandardStyle->success(sprintf(
            ' %s of them %s fixable! Just add "--fix" to console command and rerun to apply.',
            ($errorCount === $fixableErrorCount) ? 'ALL' : $fixableErrorCount,
            ($fixableErrorCount === 1) ? 'is' : 'are'
        ));
    }

    private function reportUnusedSkipped(): void
    {
        foreach ($this->skipper->getUnusedSkipped() as $skippedClass => $skippedFiles) {
            foreach ($skippedFiles as $skippedFile) {
                $this->easyCodingStandardStyle->error(sprintf(
                    'Skipped checker "%s" and file path "%s" were not found',
                    $skippedClass,
                    $skippedFile
                ));
            }
        }
    }
}
