<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix202211\Nette\Utils\Json;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use ECSPrefix202211\Symfony\Component\Console\Command\Command;
use ECSPrefix202211\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202211\Symfony\Component\Console\Input\InputOption;
use ECSPrefix202211\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Reporter\CheckerListReporter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Guard\LoadedCheckersGuard;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202211\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
final class ListCheckersCommand extends AbstractSymplifyCommand
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
    public function __construct(SniffFileProcessor $sniffFileProcessor, FixerFileProcessor $fixerFileProcessor, EasyCodingStandardStyle $easyCodingStandardStyle, CheckerListReporter $checkerListReporter, LoadedCheckersGuard $loadedCheckersGuard)
    {
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->checkerListReporter = $checkerListReporter;
        $this->loadedCheckersGuard = $loadedCheckersGuard;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('list-checkers');
        $this->setDescription('Shows loaded checkers');
        $this->addOption(Option::OUTPUT_FORMAT, null, InputOption::VALUE_REQUIRED, 'Select output format', ConsoleOutputFormatter::NAME);
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        if (!$this->loadedCheckersGuard->areSomeCheckersRegistered()) {
            return self::SUCCESS;
        }
        $totalCheckerCount = \count($this->sniffFileProcessor->getCheckers()) + \count($this->fixerFileProcessor->getCheckers());
        $outputFormat = $input->getOption(Option::OUTPUT_FORMAT);
        if ($outputFormat === 'json') {
            $sniffs = $this->sniffFileProcessor->getCheckers();
            $fixers = $this->fixerFileProcessor->getCheckers();
            $sniffClasses = \array_map(static function (Sniff $sniff) : string {
                return \get_class($sniff);
            }, $sniffs);
            \sort($sniffClasses);
            $fixerClasses = \array_map(static function (FixerInterface $fixer) : string {
                return \get_class($fixer);
            }, $fixers);
            \sort($fixerClasses);
            $data = ['sniffs' => $sniffClasses, 'fixers' => $fixerClasses];
            echo Json::encode($data, Json::PRETTY);
            return Command::SUCCESS;
        }
        $this->checkerListReporter->report($this->sniffFileProcessor->getCheckers(), 'PHP_CodeSniffer');
        $this->checkerListReporter->report($this->fixerFileProcessor->getCheckers(), 'PHP-CS-Fixer');
        $successMessage = \sprintf('Loaded %d checker%s in total', $totalCheckerCount, $totalCheckerCount === 1 ? '' : 's');
        $this->easyCodingStandardStyle->success($successMessage);
        return self::SUCCESS;
    }
}
