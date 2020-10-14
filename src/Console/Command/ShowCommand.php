<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class ShowCommand extends Command
{
    /**
     * @var int
     */
    private $checkersTotal = 0;

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
     * @var ParameterProvider
     */
    private $parameterProvider;

    public function __construct(
        SniffFileProcessor $sniffFileProcessor,
        FixerFileProcessor $fixerFileProcessor,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        ParameterProvider $parameterProvider
    ) {
        parent::__construct();

        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->parameterProvider = $parameterProvider;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Show loaded checkers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->displayCheckerList($this->sniffFileProcessor->getCheckers(), 'PHP_CodeSniffer');
        $this->displayCheckerList($this->fixerFileProcessor->getCheckers(), 'PHP-CS-Fixer');

        $successMessage = sprintf(
            'Loaded %d checker%s in total',
            $this->checkersTotal,
            $this->checkersTotal === 1 ? '' : 's'
        );
        $this->easyCodingStandardStyle->success($successMessage);

        $this->reportLoadedSets();

        return ShellCode::SUCCESS;
    }

    /**
     * @param FixerInterface[]|Sniff[]|string[] $checkers
     */
    private function displayCheckerList(array $checkers, string $type): void
    {
        if (count($checkers) === 0) {
            return;
        }

        $checkerNames = array_map(function ($fixer): string {
            return is_string($fixer) ? $fixer : get_class($fixer);
        }, $checkers);

        $this->checkersTotal += count($checkers);

        $sectionMessage = sprintf('%d checker%s from %s:', count($checkers), count($checkers) === 1 ? '' : 's', $type);
        $this->easyCodingStandardStyle->section($sectionMessage);

        sort($checkerNames);
        $this->easyCodingStandardStyle->listing($checkerNames);
    }

    private function reportLoadedSets(): void
    {
        $sets = (array) $this->parameterProvider->provideParameter(Option::SETS);
        if ($sets === []) {
            return;
        }

        $this->easyCodingStandardStyle->newLine(2);

        $this->easyCodingStandardStyle->title('Loaded Sets');

        sort($sets);

        foreach ($sets as $set) {
            $filename = realpath($set);
            $this->easyCodingStandardStyle->writeln(' * ' . $filename);
        }

        $message = sprintf('%d loaded sets', count($sets));
        $this->easyCodingStandardStyle->success($message);
    }
}
