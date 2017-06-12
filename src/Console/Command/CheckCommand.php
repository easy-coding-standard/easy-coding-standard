<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\Application;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\Console\Output\InfoMessagePrinter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Skipper;

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
     * @var InfoMessagePrinter
     */
    private $infoMessagePrinter;

    /**
     * @var Skipper
     */
    private $skipper;

    public function __construct(
        Application $applicationRunner,
        EasyCodingStandardStyle $style,
        InfoMessagePrinter $infoMessagePrinter,
        Skipper $skipper
    ) {
        parent::__construct();

        $this->applicationRunner = $applicationRunner;
        $this->style = $style;
        $this->infoMessagePrinter = $infoMessagePrinter;
        $this->skipper = $skipper;
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
        $runCommand = RunCommand::createForSourceFixerAndClearCache(
            $input->getArgument('source'),
            $input->getOption('fix'),
            (bool) $input->getOption('clear-cache')
        );

        $this->applicationRunner->runCommand($runCommand);

        if ($this->infoMessagePrinter->hasSomeErrorMessages()) {
            $this->infoMessagePrinter->printFoundErrorsStatus($input->getOption('fix'));
            $this->reportUnusedSkipped();

            return 1;
        }

        $this->style->newLine();
        $this->style->success('No errors found!');
        $this->reportUnusedSkipped();

        return 0;
    }

    private function reportUnusedSkipped(): void
    {
        if (! count($this->skipper->getUnusedSkipped())) {
            return;
        }

        foreach ($this->skipper->getUnusedSkipped() as $skippedClass => $skippedFiles) {
            foreach ($skippedFiles as $skippedFile) {
                $this->style->error(sprintf(
                    'Skipped checker "%s" and skipped "%s" were not found',
                    $skippedClass,
                    $skippedFile
                ));
            }
        }
    }
}
