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
        $this->setDescription('Show loaded checkers and their configuration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->displayCheckerList($this->sniffFileProcessor->getSniffs(), 'PHP_CodeSniffer');
        $this->displayCheckerList($this->fixerFileProcessor->getFixers(), 'PHP-CS-Fixer');

        $checkersTotal = count($this->sniffFileProcessor->getSniffs()) + count($this->fixerFileProcessor->getFixers());

        $this->symfonyStyle->success(sprintf(
            'Loaded %d checkers in total',
            $checkersTotal
        ));

        $this->symfonyStyle->newLine();

        return 0;
    }

    /**
     * @param FixerInterface[]|Sniff[] $fixers
     */
    private function displayCheckerList(array $fixers, string $type): void
    {
        $checkerNames = array_map(function ($fixer) {
            return get_class($fixer);
        }, $fixers);

        $checkerCount = count($checkerNames);
        if (! $checkerCount) {
            return;
        }

        $this->symfonyStyle->section(sprintf(
            '%d checkers from %s:',
            count($checkerNames),
            $type
        ));

        sort($checkerNames);

        foreach ($checkerNames as $checkerName) {
            $this->symfonyStyle->text(' - ' . $checkerName);
        }
    }
}
