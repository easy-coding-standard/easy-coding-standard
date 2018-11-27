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
use Symplify\EasyCodingStandard\Configuration\Option;
use Symplify\EasyCodingStandard\Console\Output\CheckCommandReporter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class CheckCommand extends Command
{
    /**
     * @var Application
     */
    private $ecsApplication;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var CheckCommandReporter
     */
    private $checkCommandReporter;

    public function __construct(
        Application $application,
        Configuration $configuration,
        CheckCommandReporter $checkCommandReporter
    ) {
        parent::__construct();

        $this->ecsApplication = $application;
        $this->configuration = $configuration;
        $this->checkCommandReporter = $checkCommandReporter;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Check coding standard in one or more directories.');
        $this->addArgument(
            Option::SOURCE,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ensureSomeCheckersAreRegistered();

        $this->configuration->resolveFromInput($input);
        $processedFilesCount = $this->ecsApplication->run();

        return $this->checkCommandReporter->report($processedFilesCount);
    }

    private function ensureSomeCheckersAreRegistered(): void
    {
        $totalCheckersLoaded = $this->ecsApplication->getCheckerCount();
        if ($totalCheckersLoaded === 0) {
            throw new NoCheckersLoadedException(
                'No checkers were found. Registers them in your config in "services:" '
                . 'section, load them via "--config <file>.yml" or "--level <level> option.'
            );
        }
    }
}
