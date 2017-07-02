<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\Application;
use Symplify\EasyCodingStandard\Configuration\Configuration;
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

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        Application $applicationRunner,
        EasyCodingStandardStyle $style,
        InfoMessagePrinter $infoMessagePrinter,
        Skipper $skipper,
        Configuration $configuration
    ) {
        parent::__construct();

        $this->applicationRunner = $applicationRunner;
        $this->style = $style;
        $this->infoMessagePrinter = $infoMessagePrinter;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
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
        $this->configuration->resolveFromInput($input);

        $this->applicationRunner->run();

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
