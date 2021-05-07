<?php

namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Reporter\CheckerListReporter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
final class ShowCommand extends AbstractSymplifyCommand
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
     * @param \Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor $sniffFileProcessor
     * @param \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor $fixerFileProcessor
     * @param \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle
     * @param \Symplify\EasyCodingStandard\Console\Reporter\CheckerListReporter $checkerListReporter
     */
    public function __construct($sniffFileProcessor, $fixerFileProcessor, $easyCodingStandardStyle, $checkerListReporter)
    {
        parent::__construct();
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->checkerListReporter = $checkerListReporter;
    }
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Show loaded checkers');
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @param \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute($input, $output)
    {
        $totalCheckerCount = \count($this->sniffFileProcessor->getCheckers()) + \count($this->fixerFileProcessor->getCheckers());
        $this->checkerListReporter->report($this->sniffFileProcessor->getCheckers(), 'PHP_CodeSniffer');
        $this->checkerListReporter->report($this->fixerFileProcessor->getCheckers(), 'PHP-CS-Fixer');
        $successMessage = \sprintf('Loaded %d checker%s in total', $totalCheckerCount, $totalCheckerCount === 1 ? '' : 's');
        $this->easyCodingStandardStyle->success($successMessage);
        return ShellCode::SUCCESS;
    }
}
