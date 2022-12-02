<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Application;

use PhpCsFixer\Differ\DifferInterface;
use ECSPrefix202212\Symfony\Component\Console\Command\Command;
use ECSPrefix202212\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\MarkdownSnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix202212\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use ECSPrefix202212\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix202212\Symplify\SmartFileSystem\SmartFileSystem;
final class MarkdownSnippetFormatterApplication
{
    /**
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter
     */
    private $snippetReporter;
    /**
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Formatter\MarkdownSnippetFormatter
     */
    private $markdownSnippetFormatter;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var \Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter
     */
    private $processedFileReporter;
    /**
     * @var \PhpCsFixer\Differ\DifferInterface
     */
    private $differ;
    /**
     * @var \Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;
    public function __construct(SnippetReporter $snippetReporter, MarkdownSnippetFormatter $markdownSnippetFormatter, SmartFileSystem $smartFileSystem, SymfonyStyle $symfonyStyle, ProcessedFileReporter $processedFileReporter, DifferInterface $differ, ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
        $this->snippetReporter = $snippetReporter;
        $this->markdownSnippetFormatter = $markdownSnippetFormatter;
        $this->smartFileSystem = $smartFileSystem;
        $this->symfonyStyle = $symfonyStyle;
        $this->processedFileReporter = $processedFileReporter;
        $this->differ = $differ;
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     */
    public function processFileInfosWithSnippetPattern(Configuration $configuration, array $fileInfos) : int
    {
        $sources = $configuration->getSources();
        $fileCount = \count($fileInfos);
        if ($fileCount === 0) {
            $this->snippetReporter->reportNoFilesFound($sources);
            return Command::SUCCESS;
        }
        $this->symfonyStyle->progressStart($fileCount);
        $errorsAndDiffs = [];
        foreach ($fileInfos as $fileInfo) {
            $fileDiff = $this->processFileInfoWithPattern($fileInfo, $configuration);
            if ($fileDiff instanceof FileDiff) {
                $errorsAndDiffs[Bridge::FILE_DIFFS][] = $fileDiff;
            }
            $this->symfonyStyle->progressAdvance();
        }
        return $this->processedFileReporter->report($errorsAndDiffs, $configuration);
    }
    private function processFileInfoWithPattern(SmartFileInfo $phpFileInfo, Configuration $configuration) : ?FileDiff
    {
        $fixedContent = $this->markdownSnippetFormatter->format($phpFileInfo, $configuration);
        $originalContent = $phpFileInfo->getContents();
        if ($phpFileInfo->getContents() === $fixedContent) {
            // nothing has changed
            return null;
        }
        if (!$configuration->isFixer()) {
            return null;
        }
        $this->smartFileSystem->dumpFile($phpFileInfo->getPathname(), $fixedContent);
        $diff = $this->differ->diff($originalContent, $fixedContent);
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);
        return new FileDiff(
            $phpFileInfo->getRelativeFilePathFromCwd(),
            $diff,
            $consoleFormattedDiff,
            // @todo
            []
        );
    }
}
