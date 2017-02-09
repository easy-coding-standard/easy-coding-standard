<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symplify\EasyCodingStandard\Application\ApplicationRunner;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFileLoader;
use Symplify\EasyCodingStandard\Console\Output\InfoMessagePrinter;

final class RunCommand extends Command
{
    /**
     * @var StyleInterface
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
        StyleInterface $style,
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->applicationRunner->runCommand(
            RunApplicationCommand::createFromInputAndData($input, $this->multiCsFileLoader->load())
        );

        if ($this->infoMessagePrinter->hasSomeErrorMessages()) {
            $this->infoMessagePrinter->printFoundErrorsStatus($input->getOption('fix'));

            return 1;
        }

        $this->style->success(sprintf(
            'Sources "%s" were checked!',
            implode(',', $input->getArgument('source'))
        ));

        return 0;
    }
}
