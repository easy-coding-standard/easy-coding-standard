<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\Application;
use Symplify\EasyCodingStandard\Application\Command\RunCommandFactory;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFileLoader;
use Symplify\EasyCodingStandard\Console\Output\InfoMessagePrinter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;

final class CheckCommand extends Command
{
    /**
     * @var string
     */
    private const NAME = 'check';

    /**
     * @var EasyCodingStandardStyle
     */
    private $style;

    /**
     * @var Application
     */
    private $applicationRunner;

    /**
     * @var ConfigurationFileLoader
     */
    private $configurationFileLoader;

    /**
     * @var InfoMessagePrinter
     */
    private $infoMessagePrinter;

    /**
     * @var RunCommandFactory
     */
    private $runCommandFactory;

    public function __construct(
        Application $applicationRunner,
        EasyCodingStandardStyle $style,
        ConfigurationFileLoader $configurationFileLoader,
        InfoMessagePrinter $infoMessagePrinter,
        RunCommandFactory $runCommandFactory
    ) {
        parent::__construct();

        $this->applicationRunner = $applicationRunner;
        $this->style = $style;
        $this->configurationFileLoader = $configurationFileLoader;
        $this->infoMessagePrinter = $infoMessagePrinter;
        $this->runCommandFactory = $runCommandFactory;
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
        $runCommand = $this->runCommandFactory->create(
            $input->getArgument('source'),
            $input->getOption('fix'),
            (bool) $input->getOption('clear-cache'),
            $this->configurationFileLoader->load()
        );

        $this->applicationRunner->runCommand($runCommand);

        if ($this->infoMessagePrinter->hasSomeErrorMessages()) {
            $this->infoMessagePrinter->printFoundErrorsStatus($input->getOption('fix'));

            return 1;
        }

        $this->style->newLine();
        $this->style->success('No errors found!');

        return 0;
    }
}
