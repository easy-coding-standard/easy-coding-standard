<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffResultFactory;
use Symplify\EasyCodingStandard\Guard\LoadedCheckersGuard;
use Symplify\EasyCodingStandard\ValueObject\Option;

abstract class AbstractCheckCommand extends Command
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var EasyCodingStandardStyle
     */
    protected $easyCodingStandardStyle;

    /**
     * @var EasyCodingStandardApplication
     */
    protected $easyCodingStandardApplication;

    /**
     * @var OutputFormatterCollector
     */
    private $outputFormatterCollector;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var ErrorAndDiffResultFactory
     */
    private $errorAndDiffResultFactory;

    /**
     * @var LoadedCheckersGuard
     */
    private $loadedCheckersGuard;

    /**
     * @required
     */
    public function autowireAbstractCheckCommand(
        Configuration $configuration,
        EasyCodingStandardApplication $easyCodingStandardApplication,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        OutputFormatterCollector $outputFormatterCollector,
        ErrorAndDiffCollector $errorAndDiffCollector,
        ErrorAndDiffResultFactory $errorAndDiffResultFactory,
        LoadedCheckersGuard $loadedCheckersGuard
    ): void {
        $this->configuration = $configuration;
        $this->easyCodingStandardApplication = $easyCodingStandardApplication;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->outputFormatterCollector = $outputFormatterCollector;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->errorAndDiffResultFactory = $errorAndDiffResultFactory;
        $this->loadedCheckersGuard = $loadedCheckersGuard;
    }

    protected function configure(): void
    {
        $this->addArgument(
            Option::PATHS,
            // optional is on purpose here, since path from ecs.php can se ubsed
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The path(s) to be checked.'
        );

        $this->addOption(Option::FIX, null, null, 'Fix found violations.');

        $this->addOption(Option::CLEAR_CACHE, null, null, 'Clear cache for already checked files.');

        $this->addOption(
            Option::NO_PROGRESS_BAR,
            null,
            InputOption::VALUE_NONE,
            'Hide progress bar. Useful e.g. for nicer CI output.'
        );

        $this->addOption(
            Option::NO_ERROR_TABLE,
            null,
            InputOption::VALUE_NONE,
            'Hide error table. Useful e.g. for fast check of error count.'
        );

        $this->addOption(
            Option::OUTPUT_FORMAT,
            null,
            InputOption::VALUE_REQUIRED,
            'Select output format',
            ConsoleOutputFormatter::NAME
        );

        $this->addOption(
            Option::MATCH_GIT_DIFF,
            null,
            InputOption::VALUE_NONE,
            'Execute only on file(s) matching the git diff.'
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->loadedCheckersGuard->ensureSomeCheckersAreRegistered();
    }

    protected function reportProcessedFiles(int $processedFileCount): int
    {
        $outputFormat = $this->configuration->getOutputFormat();
        $outputFormatter = $this->outputFormatterCollector->getByName($outputFormat);

        $errorAndDiffResult = $this->errorAndDiffResultFactory->create($this->errorAndDiffCollector);
        return $outputFormatter->report($errorAndDiffResult, $processedFileCount);
    }
}
