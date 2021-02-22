<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;

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

    protected function configure(): void
    {
        $this->setDescription('Check coding standard in one or more directories.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration->resolveFromInput($input);

        // CLI paths override parameter paths
        if ($this->configuration->getSources() === []) {
            $this->configuration->setSources($this->configuration->getPaths());
        }

        $processedFilesCount = $this->easyCodingStandardApplication->run();

        return $this->processedFileReporter->report($processedFilesCount);
    }
}
