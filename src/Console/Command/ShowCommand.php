<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20210629\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210629\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Reporter\CheckerListReporter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Guard\LoadedCheckersGuard;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use ECSPrefix20210629\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use ECSPrefix20210629\Symplify\PackageBuilder\Console\ShellCode;
final class ShowCommand extends \ECSPrefix20210629\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor
     */
    private $sniffFileProcessor;
    /**
     * @var \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor
     */
    private $fixerFileProcessor;
    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;
    /**
     * @var \Symplify\EasyCodingStandard\Console\Reporter\CheckerListReporter
     */
    private $checkerListReporter;
    /**
     * @var \Symplify\EasyCodingStandard\Guard\LoadedCheckersGuard
     */
    private $loadedCheckersGuard;
    public function __construct(\Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor $sniffFileProcessor, \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor $fixerFileProcessor, \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle, \Symplify\EasyCodingStandard\Console\Reporter\CheckerListReporter $checkerListReporter, \Symplify\EasyCodingStandard\Guard\LoadedCheckersGuard $loadedCheckersGuard)
    {
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->checkerListReporter = $checkerListReporter;
        $this->loadedCheckersGuard = $loadedCheckersGuard;
        parent::__construct();
    }
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Show loaded checkers');
    }
    protected function execute(\ECSPrefix20210629\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20210629\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        if (!$this->loadedCheckersGuard->areSomeCheckersRegistered()) {
            $this->loadedCheckersGuard->report();
            return \ECSPrefix20210629\Symplify\PackageBuilder\Console\ShellCode::ERROR;
        }
        $totalCheckerCount = \count($this->sniffFileProcessor->getCheckers()) + \count($this->fixerFileProcessor->getCheckers());
        $this->checkerListReporter->report($this->sniffFileProcessor->getCheckers(), 'PHP_CodeSniffer');
        $this->checkerListReporter->report($this->fixerFileProcessor->getCheckers(), 'PHP-CS-Fixer');
        $successMessage = \sprintf('Loaded %d checker%s in total', $totalCheckerCount, $totalCheckerCount === 1 ? '' : 's');
        $this->easyCodingStandardStyle->success($successMessage);
        return \ECSPrefix20210629\Symplify\PackageBuilder\Console\ShellCode::SUCCESS;
    }
}
