<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix202302\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202302\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\Configuration\ConfigInitializer;
use Symplify\EasyCodingStandard\MemoryLimitter;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
final class CheckCommand extends \Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand
{
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter
     */
    private $processedFileReporter;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\MemoryLimitter
     */
    private $memoryLimitter;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Configuration\ConfigInitializer
     */
    private $configInitializer;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication
     */
    private $easyCodingStandardApplication;
    public function __construct(ProcessedFileReporter $processedFileReporter, MemoryLimitter $memoryLimitter, ConfigInitializer $configInitializer, EasyCodingStandardApplication $easyCodingStandardApplication)
    {
        $this->processedFileReporter = $processedFileReporter;
        $this->memoryLimitter = $memoryLimitter;
        $this->configInitializer = $configInitializer;
        $this->easyCodingStandardApplication = $easyCodingStandardApplication;
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
        if (!$this->configInitializer->areSomeCheckersRegistered()) {
            $this->configInitializer->createConfig(\getcwd());
            return self::SUCCESS;
        }
        $configuration = $this->configurationFactory->createFromInput($input);
        $this->memoryLimitter->adjust($configuration);
        $errorsAndDiffs = $this->easyCodingStandardApplication->run($configuration, $input);
        return $this->processedFileReporter->report($errorsAndDiffs, $configuration);
    }
}
