<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\ApplicationRunner;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFileLoader;
use Symplify\EasyCodingStandard\Console\Output\InfoMessagePrinter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;

final class RunCommand extends Command
{
    /**
     * @var EasyCodingStandardStyle
     */
    private $style;

    /**
     * @var ApplicationRunner
     */
    private $applicationRunner;

    /**
     * @var ConfigurationFileLoader
     */
    private $multiCsFileLoader;

    /**
     * @var InfoMessagePrinter
     */
    private $infoMessagePrinter;

    public function __construct(
        ApplicationRunner $applicationRunner,
        EasyCodingStandardStyle $style,
        ConfigurationFileLoader $multiCsFileLoader,
        InfoMessagePrinter $infoMessagePrinter
    ) {
        parent::__construct();

        $this->applicationRunner = $applicationRunner;
        $this->style = $style;
        $this->multiCsFileLoader = $multiCsFileLoader;
        $this->infoMessagePrinter = $infoMessagePrinter;
    }

    protected function configure()
    {
        $this->setName('run');
        $this->addArgument('source', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The path(s) to be checked.');
        $this->addOption('fix', null, null, 'Fix found violations.');
        $this->setDescription('Check coding standard in one or more directories.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $runCommand = RunApplicationCommand::createFromInputAndData($input, $this->multiCsFileLoader->load());

        $this->applicationRunner->runCommand($runCommand);

        if ($this->infoMessagePrinter->hasSomeErrorMessages()) {
            $this->infoMessagePrinter->printFoundErrorsStatus($input->getOption('fix'));

            return 1;
        }

        $this->style->success('No errors found!');

        return 0;
    }
}
