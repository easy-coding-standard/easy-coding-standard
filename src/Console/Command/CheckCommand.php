<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix202208\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202208\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\MemoryLimitter;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
final class CheckCommand extends \Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter
     */
    private $processedFileReporter;
    /**
     * @var \Symplify\EasyCodingStandard\MemoryLimitter
     */
    private $memoryLimitter;
    public function __construct(ProcessedFileReporter $processedFileReporter, MemoryLimitter $memoryLimitter)
    {
        $this->processedFileReporter = $processedFileReporter;
        $this->memoryLimitter = $memoryLimitter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check');
        $this->setDescription('Check coding standard in one or more directories.');
        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        if (!$this->loadedCheckersGuard->areSomeCheckersRegistered()) {
            $this->loadedCheckersGuard->report();
            return self::FAILURE;
        }
        $configuration = $this->configurationFactory->createFromInput($input);
        $this->memoryLimitter->adjust($configuration);
        $errorsAndDiffs = $this->easyCodingStandardApplication->run($configuration, $input);
        return $this->processedFileReporter->report($errorsAndDiffs, $configuration);
    }
}
