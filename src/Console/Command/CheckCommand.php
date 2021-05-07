<?php

namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
final class CheckCommand extends \Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand
{
    /**
     * @var ProcessedFileReporter
     */
    private $processedFileReporter;
    /**
     * @param \Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter $processedFileReporter
     */
    public function __construct($processedFileReporter)
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
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @param \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute($input, $output)
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
