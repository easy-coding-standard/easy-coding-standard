<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

final class ShowCommand extends Command
{
    /**
     * @var string
     */
    private const NAME = 'show';

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var int
     */
    private $checkersTotal = 0;

    public function __construct(
        SniffFileProcessor $sniffFileProcessor,
        FixerFileProcessor $fixerFileProcessor,
        SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();

        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->symfonyStyle = $symfonyStyle;
    }

    protected function configure(): void
    {
        $this->setName(self::NAME);
        $this->setDescription('Show loaded checkers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->displayCheckerList($this->sniffFileProcessor->getCheckers(), 'PHP_CodeSniffer');
        $this->displayCheckerList($this->fixerFileProcessor->getCheckers(), 'PHP-CS-Fixer');

        $this->symfonyStyle->success(sprintf(
            'Loaded %d checker%s in total',
            $this->checkersTotal,
            $this->checkersTotal === 1 ? '' : 's'
        ));

        $this->symfonyStyle->newLine();

        return 0;
    }

    /**
     * @param FixerInterface[]|Sniff[]|string[] $checkers
     */
    private function displayCheckerList(array $checkers, string $type): void
    {
        if (! count($checkers)) {
            return;
        }

        $checkerNames = array_map(function ($fixer) {
            return is_string($fixer) ? $fixer : get_class($fixer);
        }, $checkers);

        $this->checkersTotal += count($checkers);

        $this->symfonyStyle->section(sprintf(
            '%d checker%s from %s:',
            count($checkers),
            count($checkers) === 1 ? '' : 's',
            $type
        ));

        sort($checkerNames);
        foreach ($checkerNames as $checkerName) {
            $this->symfonyStyle->text('- ' . $checkerName);
        }
    }
}
