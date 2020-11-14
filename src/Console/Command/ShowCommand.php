<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Reporter\CheckerListReporter;
use Symplify\EasyCodingStandard\Console\Reporter\SetsReporter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Console\ShellCode;

final class ShowCommand extends Command
{
    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var CheckerListReporter
     */
    private $checkerListReporter;

    /**
     * @var SetsReporter
     */
    private $setsReporter;

    public function __construct(
        SniffFileProcessor $sniffFileProcessor,
        FixerFileProcessor $fixerFileProcessor,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        CheckerListReporter $checkerListReporter,
        SetsReporter $setsReporter
    ) {
        parent::__construct();

        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->checkerListReporter = $checkerListReporter;
        $this->setsReporter = $setsReporter;
    }

    protected function configure(): void
    {
        $this->setDescription('Show loaded checkers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $totalCheckerCount = count($this->sniffFileProcessor->getCheckers()) + count(
            $this->fixerFileProcessor->getCheckers()
        );

        $this->checkerListReporter->report($this->sniffFileProcessor->getCheckers(), 'PHP_CodeSniffer');
        $this->checkerListReporter->report($this->fixerFileProcessor->getCheckers(), 'PHP-CS-Fixer');

        $successMessage = sprintf(
            'Loaded %d checker%s in total',
            $totalCheckerCount,
            $totalCheckerCount === 1 ? '' : 's'
        );
        $this->easyCodingStandardStyle->success($successMessage);

        $this->setsReporter->report();

        return ShellCode::SUCCESS;
    }
}
