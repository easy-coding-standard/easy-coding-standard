<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Application;

use PhpCsFixer\Differ\DifferInterface;
use ECSPrefix20220220\Symfony\Component\Console\Command\Command;
use ECSPrefix20220220\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix20220220\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem;
final class SnippetFormatterApplication
{
    /**
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter
     */
    private $snippetReporter;
    /**
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter
     */
    private $snippetFormatter;
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
    public function __construct(\Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter $snippetReporter, \Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter $snippetFormatter, \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \ECSPrefix20220220\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter $processedFileReporter, \PhpCsFixer\Differ\DifferInterface $differ, \ECSPrefix20220220\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
        $this->snippetReporter = $snippetReporter;
        $this->snippetFormatter = $snippetFormatter;
        $this->smartFileSystem = $smartFileSystem;
        $this->symfonyStyle = $symfonyStyle;
        $this->processedFileReporter = $processedFileReporter;
        $this->differ = $differ;
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     */
    public function processFileInfosWithSnippetPattern(\Symplify\EasyCodingStandard\ValueObject\Configuration $configuration, array $fileInfos, string $snippetPattern, string $kind) : int
    {
        $sources = $configuration->getSources();
        $fileCount = \count($fileInfos);
        if ($fileCount === 0) {
            $this->snippetReporter->reportNoFilesFound($sources);
            return \ECSPrefix20220220\Symfony\Component\Console\Command\Command::SUCCESS;
        }
        $this->symfonyStyle->progressStart($fileCount);
        $errorsAndDiffs = [];
        foreach ($fileInfos as $fileInfo) {
            $errorsAndDiffs = \array_merge($errorsAndDiffs, $this->processFileInfoWithPattern($fileInfo, $snippetPattern, $kind, $configuration));
            $this->symfonyStyle->progressAdvance();
        }
        return $this->processedFileReporter->report($errorsAndDiffs, $configuration);
    }
    /**
     * @return array<string, array<FileDiff>>
     */
    private function processFileInfoWithPattern(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $phpFileInfo, string $snippetPattern, string $kind, \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration) : array
    {
        $fixedContent = $this->snippetFormatter->format($phpFileInfo, $snippetPattern, $kind, $configuration);
        $originalContent = $phpFileInfo->getContents();
        if ($phpFileInfo->getContents() === $fixedContent) {
            // nothing has changed
            return [];
        }
        if (!$configuration->isFixer()) {
            return [];
        }
        $this->smartFileSystem->dumpFile($phpFileInfo->getPathname(), $fixedContent);
        $diff = $this->differ->diff($originalContent, $fixedContent);
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);
        $fileDiff = new \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff(
            $phpFileInfo->getRelativeFilePathFromCwd(),
            $diff,
            $consoleFormattedDiff,
            // @todo
            []
        );
        return ['files_diffs' => [$fileDiff]];
    }
}
