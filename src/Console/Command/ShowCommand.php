<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\CheckerSetExtractor\FixerSetExtractor;
use Symplify\EasyCodingStandard\CheckerSetExtractor\SniffSetExtractor;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

final class ShowCommand extends Command
{
    /**
     * @var string
     */
    private const NAME = 'show';

    /**
     * @var string
     */
    private const OPTION_FIXER_SET_NAME = 'fixer-set';

    /**
     * @var string
     */
    private const OPTION_SNIFF_SET_NAME = 'sniff-set';

    /**
     * @var string
     */
    private const OPTION_WITH_CONFIG = 'with-config';

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
     * @var FixerSetExtractor
     */
    private $fixerSetExtractor;

    /**
     * @var int
     */
    private $checkersTotal = 0;

    /**
     * @var SniffSetExtractor
     */
    private $sniffSetExtractor;

    public function __construct(
        SniffFileProcessor $sniffFileProcessor,
        FixerFileProcessor $fixerFileProcessor,
        SymfonyStyle $symfonyStyle,
        FixerSetExtractor $fixerSetExtractor,
        SniffSetExtractor $sniffSetExtractor
    ) {
        parent::__construct();

        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->symfonyStyle = $symfonyStyle;
        $this->fixerSetExtractor = $fixerSetExtractor;
        $this->sniffSetExtractor = $sniffSetExtractor;
    }

    protected function configure(): void
    {
        $this->setName(self::NAME);
        $this->setDescription('Show loaded checkers and their configuration.');
        $this->addOption(
            self::OPTION_FIXER_SET_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Show fixers in PHP-CS-Fixer sets (@PSR1, @PSR2, @Symfony...)'
        );
        $this->addOption(
            self::OPTION_SNIFF_SET_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Show fixers in PHP_CodeSniffer sets (PEAR, PHPCS, PSR1, PSR2, Squiz and Zend)'
        );
        $this->addOption(
            self::OPTION_WITH_CONFIG,
            null,
            InputOption::VALUE_NONE,
            'Show also specific checker configuration'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fixerSetName = $input->getOption(self::OPTION_FIXER_SET_NAME);
        if ($fixerSetName) {
            $fixerSet = $this->fixerSetExtractor->extract($fixerSetName);
            $type = 'PHP-CS-Fixer - fixer set ' . $fixerSetName;

            if ($input->getOption(self::OPTION_WITH_CONFIG)) {
                $this->displayCheckerListWithConfig($fixerSet, $type);
            } else {
                $fixerNames = array_keys($fixerSet);
                $this->displayCheckerList($fixerNames, $type);
            }
        }

        $sniffSetName = $input->getOption(self::OPTION_SNIFF_SET_NAME);
        if ($sniffSetName) {
            $sniffSet = $this->sniffSetExtractor->extract($sniffSetName);
            $type = 'PHP-CS-Fixer - sniff set ' . $sniffSetName;

            if ($input->getOption(self::OPTION_WITH_CONFIG)) {
                $this->displayCheckerListWithConfig($sniffSet, $type);
            } else {
                $sniffNames = array_keys($sniffSet);
                $this->displayCheckerList($sniffNames, $type);
            }
        } else {
            $this->displayCheckerList($this->sniffFileProcessor->getSniffs(), 'PHP_CodeSniffer');
            $this->displayCheckerList($this->fixerFileProcessor->getFixers(), 'PHP-CS-Fixer');
        }

        $this->symfonyStyle->success(sprintf(
            'Loaded %d checkers in total',
            $this->checkersTotal
        ));

        $this->symfonyStyle->newLine();

        return 0;
    }

    /**
     * @param FixerInterface[]|Sniff[]|string $fixers
     */
    private function displayCheckerList(array $fixers, string $type): void
    {
        $checkerNames = array_map(function ($fixer) {
            return is_string($fixer) ? $fixer : get_class($fixer);
        }, $fixers);

        $checkerCount = count($checkerNames);
        if (! $checkerCount) {
            return;
        }

        $this->checkersTotal += $checkerCount;

        $this->symfonyStyle->section(sprintf(
            '%d checkers from %s:',
            count($checkerNames),
            $type
        ));

        sort($checkerNames);
        foreach ($checkerNames as $checkerName) {
            $this->symfonyStyle->text('- ' . $checkerName);
        }
    }

    /**
     * @param mixed[] $checkerSet
     */
    private function displayCheckerListWithConfig(array $checkerSet, string $type): void
    {
        $this->checkersTotal += count($checkerSet);

        foreach ($checkerSet as $checkerName => $config) {
            if (! is_array($config)) {
                $this->symfonyStyle->text('   - ' . $checkerName);

                continue;
            }

            $this->symfonyStyle->text('   ' . $checkerName . ':');
            foreach ($config as $option => $value) {
                if (! is_array($value)) {
                    $optionWithSeparator = is_numeric($option) ? '-' : $option . ':';
                    $value = ($value === true ? 'true' : $value);
                    $value = ($value === false ? 'false' : $value);
                    $this->symfonyStyle->text('       ' . $optionWithSeparator . ' ' . $value);
                } else {
                    $this->symfonyStyle->text('       ' . $option . ':');
                    foreach ($value as $subValue) {
                        $this->symfonyStyle->text('           - ' . $subValue);
                    }
                }
            }
        }
    }
}
