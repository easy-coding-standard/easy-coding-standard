<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Configuration\Exception\NoDirectoryException;
use Symplify\EasyCodingStandard\Configuration\Exception\NoPHPFileException;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\HeredocNowdoc\HeredocNowdocPHPCodeFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class CheckHeredocNowdocCommand extends Command
{
    /**
     * @var string
     */
    private const SOURCE = 'source';

    /**
     * @var string
     */
    private const NO_STRICT_TYPES_DECLARATION = 'no-strict-types-declaration';

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var HeredocNowdocPHPCodeFormatter
     */
    private $heredocnowdocPHPCodeFormatter;

    /**
     * @var OutputFormatterCollector
     */
    private $outputFormatterCollector;

    public function __construct(
        SmartFileSystem $smartFileSystem,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        HeredocNowdocPHPCodeFormatter $heredocnowdocPHPCodeFormatter,
        OutputFormatterCollector $outputFormatterCollector,
        Configuration $configuration
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->heredocnowdocPHPCodeFormatter = $heredocnowdocPHPCodeFormatter;
        $this->outputFormatterCollector = $outputFormatterCollector;
        $this->configuration = $configuration;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Format Heredoc/Nowdoc PHP code');
        $this->addArgument(
            self::SOURCE,
            InputArgument::REQUIRED,
            'Path to the directory containing PHP Code with Heredoc/Nowdoc inside'
        );
        $this->addOption(self::NO_STRICT_TYPES_DECLARATION, null, null, 'No strict types declaration');
        $this->addOption(Option::FIX, null, null, 'Fix found violations.');
        $this->addOption(
            Option::OUTPUT_FORMAT,
            null,
            InputOption::VALUE_REQUIRED,
            'Select output format',
            ConsoleOutputFormatter::NAME
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $heredocNowDocDirectory */
        $heredocNowDocDirectory = $input->getArgument(self::SOURCE);
        $this->verifyDirectory($heredocNowDocDirectory);

        $finder = new Finder();
        $this->verifyHasFiles($finder, $heredocNowDocDirectory);

        $noStrictTypesDeclaration = (bool) $input->getOption(self::NO_STRICT_TYPES_DECLARATION);
        $fix = (bool) $input->getOption(Option::FIX);
        $alreadyFollowCodingStandard = true;

        $countFixable = 0;
        $outputFormatter = null;
        foreach ($finder as $file) {
            $absoluteFilePath = $file->getRealPath();

            $phpFileInfo = new SmartFileInfo($absoluteFilePath);
            $fixedContent = $this->heredocnowdocPHPCodeFormatter->format($phpFileInfo, $noStrictTypesDeclaration);

            if ($phpFileInfo->getContents() === $fixedContent) {
                continue;
            }

            $alreadyFollowCodingStandard = false;
            if ($fix) {
                $this->smartFileSystem->dumpFile($absoluteFilePath, (string) $fixedContent);
                continue;
            }

            $this->configuration->resolveFromArray(['isFixer' => false]);
            $outputFormat = $this->resolveOutputFormat($input);

            /** @var ConsoleOutputFormatter $outputFormatter */
            $outputFormatter = $this->outputFormatterCollector->getByName($outputFormat);
            $outputFormatter->addCustomFileName($absoluteFilePath);
            ++$countFixable;
        }

        if ($outputFormatter && $countFixable > 0) {
            return $outputFormatter->report($countFixable);
        }

        if ($alreadyFollowCodingStandard) {
            $successMessage = 'PHP code in Heredoc/Nowdoc already follow coding standard';
        } else {
            $successMessage = 'PHP code in Heredoc/Nowdoc has been fixed to follow coding standard';
        }

        $this->easyCodingStandardStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }

    private function verifyDirectory(string $heredocNowDocDirectory): void
    {
        if (! is_dir($heredocNowDocDirectory)) {
            $message = sprintf('Directory "%s" not found', $heredocNowDocDirectory);
            throw new NoDirectoryException($message);
        }
    }

    private function verifyHasFiles(Finder $finder, string $heredocNowDocDirectory): void
    {
        $finder->files()->in($heredocNowDocDirectory)->name('*.php');

        if (! $finder->hasResults()) {
            $message = sprintf('No file in "%s"', $heredocNowDocDirectory);
            throw new NoPHPFileException($message);
        }
    }

    private function resolveOutputFormat(InputInterface $input): string
    {
        $outputFormat = (string) $input->getOption(Option::OUTPUT_FORMAT);

        // Backwards compatibility with older version
        if ($outputFormat === 'table') {
            return ConsoleOutputFormatter::NAME;
        }

        return $outputFormat;
    }
}
