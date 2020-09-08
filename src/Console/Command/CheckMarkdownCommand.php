<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Configuration\Exception\NoMarkdownFileException;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Markdown\MarkdownPHPCodeFormatter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class CheckMarkdownCommand extends Command
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
     * @var MarkdownPHPCodeFormatter
     */
    private $markdownPHPCodeFormatter;

    public function __construct(
        SmartFileSystem $smartFileSystem,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        MarkdownPHPCodeFormatter $markdownPHPCodeFormatter
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;

        parent::__construct();

        $this->markdownPHPCodeFormatter = $markdownPHPCodeFormatter;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Format Markdown PHP code');
        $this->addArgument(self::SOURCE, InputArgument::REQUIRED, 'Path to the Markdown file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $markdownFile */
        $markdownFile = $input->getArgument(self::SOURCE);
        if (! $this->smartFileSystem->exists($markdownFile)) {
            $message = sprintf('Markdown file "%s" not found', $markdownFile);
            throw new NoMarkdownFileException($message);
        }

        $noStrictTypesDeclaration = (bool) $input->getOption(self::NO_STRICT_TYPES_DECLARATION);
        $markdownFileInfo = new SmartFileInfo($markdownFile);
        $fixedContent = $this->markdownPHPCodeFormatter->format($markdownFileInfo, $noStrictTypesDeclaration);

        if ($markdownFileInfo->getContents() === $fixedContent) {
            $successMessage = 'PHP code in Markdown already follow coding standard';
        } else {
            $this->smartFileSystem->dumpFile($markdownFile, (string) $fixedContent);
            $successMessage = 'PHP code in Markdown has been fixed to follow coding standard';
        }

        $this->easyCodingStandardStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }
}
