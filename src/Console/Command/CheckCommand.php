<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20211002\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
use Symplify\EasyCodingStandard\ValueObject\Option;
final class CheckCommand extends \Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter
     */
    private $processedFileReporter;
    public function __construct(\Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter $processedFileReporter)
    {
        $this->processedFileReporter = $processedFileReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setDescription('Check coding standard in one or more directories.');
        parent::configure();
    }
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute($input, $output) : int
    {
        if (!$this->loadedCheckersGuard->areSomeCheckersRegistered()) {
            $this->loadedCheckersGuard->report();
            return self::FAILURE;
        }
        $configuration = $this->configurationFactory->createFromInput($input);
        if ($configuration->doesMatchGitDiff()) {
            $errorMessage = \sprintf('The option "--%s" is deprecated and will be removed in next ECS version.%sIts purpose was to improve performance, but with parallel is not useful anymore.', \Symplify\EasyCodingStandard\ValueObject\Option::MATCH_GIT_DIFF, \PHP_EOL);
            $this->symfonyStyle->warning($errorMessage);
            \sleep(3);
        }
        $errorsAndDiffs = $this->easyCodingStandardApplication->run($configuration, $input);
        return $this->processedFileReporter->report($errorsAndDiffs, $configuration);
    }
}
