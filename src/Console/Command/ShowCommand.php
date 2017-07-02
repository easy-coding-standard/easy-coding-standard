<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

final class ShowCommand extends Command
{
    /**
     * @var string
     */
    private const NAME = 'show';

    /**
     * @var EasyCodingStandardStyle
     */
    private $style;

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    public function __construct(
        EasyCodingStandardStyle $style,
        SniffFileProcessor $sniffFileProcessor,
        FixerFileProcessor $fixerFileProcessor
    ) {
        parent::__construct();

        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->style = $style;
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

        $this->style->success(sprintf(
            'Loaded %d checkers in total',
            $checkersTotal
        ));

        $this->style->newLine();

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

        $this->style->section(sprintf(
            '%d checkers from %s:',
            count($checkerNames),
            $type
        ));

        sort($checkerNames);

        foreach ($checkerNames as $checkerName) {
            $this->style->text(' - ' . $checkerName);
        }
    }
}
