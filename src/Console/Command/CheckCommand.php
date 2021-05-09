<?php

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
use Symplify\PackageBuilder\Console\ShellCode;

final class CheckCommand extends AbstractCheckCommand
{
    /**
     * @var ProcessedFileReporter
     */
    private $processedFileReporter;

    public function __construct(ProcessedFileReporter $processedFileReporter)
    {
        $this->processedFileReporter = $processedFileReporter;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Check coding standard in one or more directories.');

        parent::configure();
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->loadedCheckersGuard->areSomeCheckerRegistered() === false) {
            $this->loadedCheckersGuard->report();
            return ShellCode::ERROR;
        }

        $this->configuration->resolveFromInput($input);

        // CLI paths override parameter paths
        if ($this->configuration->getSources() === []) {
            $this->configuration->setSources($this->configuration->getPaths());
        }

        $processedFilesCount = $this->easyCodingStandardApplication->run();

        return $this->processedFileReporter->report($processedFilesCount);
    }
}
