<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Configuration\Exception\NoDirectoryException;
use Symplify\EasyCodingStandard\Configuration\Exception\NoPHPFileException;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\HeredocNowdoc\HeredocNowdocPHPCodeFormatter;
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

    public function __construct(
        SmartFileSystem $smartFileSystem,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        HeredocNowdocPHPCodeFormatter $heredocnowdocPHPCodeFormatter
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;

        parent::__construct();

        $this->heredocnowdocPHPCodeFormatter = $heredocnowdocPHPCodeFormatter;
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $heredocNowDocDirectory */
        $heredocNowDocDirectory = $input->getArgument(self::SOURCE);
        if (! is_dir($heredocNowDocDirectory)) {
            $message = sprintf('Directory "%s" not found', $heredocNowDocDirectory);
            throw new NoDirectoryException($message);
        }

        $finder = new Finder();
        $finder->files()->in($heredocNowDocDirectory)->name('*.php');

        if (! $finder->hasResults()) {
            $message = sprintf('No file in "%s"', $heredocNowDocDirectory);
            throw new NoPHPFileException($message);
        }

        $noStrictTypesDeclaration = (bool) $input->getOption(self::NO_STRICT_TYPES_DECLARATION);
        $alreadyFollowCodingStandard = true;
        foreach ($finder as $file) {
            $absoluteFilePath = $file->getRealPath();

            $phpFileInfo = new SmartFileInfo($absoluteFilePath);
            $fixedContent = $this->heredocnowdocPHPCodeFormatter->format($phpFileInfo, $noStrictTypesDeclaration);

            if ($phpFileInfo->getContents() !== $fixedContent) {
                $this->smartFileSystem->dumpFile($absoluteFilePath, (string) $fixedContent);
                $alreadyFollowCodingStandard = false;
            }
        }

        if ($alreadyFollowCodingStandard) {
            $successMessage = 'PHP code in Heredoc/Nowdoc already follow coding standard';
        } else {
            $successMessage = 'PHP code in Heredoc/Nowdoc has been fixed to follow coding standard';
        }

        $this->easyCodingStandardStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }
}
