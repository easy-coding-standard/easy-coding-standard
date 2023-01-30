<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\Configuration\ConfigInitializer;
use Symplify\EasyCodingStandard\MemoryLimitter;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;

final class CheckCommand extends AbstractCheckCommand
{
    public function __construct(
        private readonly ProcessedFileReporter $processedFileReporter,
        private readonly MemoryLimitter $memoryLimitter,
        private readonly ConfigInitializer $configInitializer,
        private readonly EasyCodingStandardApplication $easyCodingStandardApplication,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('check');
        $this->setDescription('Check coding standard in one or more directories.');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! $this->configInitializer->areSomeCheckersRegistered()) {
            $this->configInitializer->createConfig(getcwd());
            return self::SUCCESS;
        }

        $configuration = $this->configurationFactory->createFromInput($input);
        $this->memoryLimitter->adjust($configuration);

        $errorsAndDiffs = $this->easyCodingStandardApplication->run($configuration, $input);

        return $this->processedFileReporter->report($errorsAndDiffs, $configuration);
    }
}
