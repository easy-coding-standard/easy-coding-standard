<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Application;

use ECSPrefix202301\Nette\Utils\FileSystem;
use PhpCsFixer\Differ\DifferInterface;
use ECSPrefix202301\Symfony\Component\Console\Command\Command;
use ECSPrefix202301\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\FileSystem\StaticRelativeFilePathHelper;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\MarkdownSnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix202301\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
final class MarkdownSnippetFormatterApplication
{
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter
     */
    private $snippetReporter;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Formatter\MarkdownSnippetFormatter
     */
    private $markdownSnippetFormatter;
    /**
     * @readonly
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fileSystem;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter
     */
    private $processedFileReporter;
    /**
     * @readonly
     * @var \PhpCsFixer\Differ\DifferInterface
     */
    private $differ;
    /**
     * @readonly
     * @var \Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;
    public function __construct(SnippetReporter $snippetReporter, MarkdownSnippetFormatter $markdownSnippetFormatter, \ECSPrefix202301\Symfony\Component\Filesystem\Filesystem $fileSystem, SymfonyStyle $symfonyStyle, ProcessedFileReporter $processedFileReporter, DifferInterface $differ, ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
        $this->snippetReporter = $snippetReporter;
        $this->markdownSnippetFormatter = $markdownSnippetFormatter;
        $this->fileSystem = $fileSystem;
        $this->symfonyStyle = $symfonyStyle;
        $this->processedFileReporter = $processedFileReporter;
        $this->differ = $differ;
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }
    /**
     * @param string[] $filePaths
     */
    public function processFileInfosWithSnippetPattern(Configuration $configuration, array $filePaths) : int
    {
        $sources = $configuration->getSources();
        $fileCount = \count($filePaths);
        if ($fileCount === 0) {
            $this->snippetReporter->reportNoFilesFound($sources);
            return Command::SUCCESS;
        }
        $this->symfonyStyle->progressStart($fileCount);
        $errorsAndDiffs = [];
        foreach ($filePaths as $filePath) {
            $fileDiff = $this->processFilePathWithPattern($filePath, $configuration);
            if ($fileDiff instanceof FileDiff) {
                $errorsAndDiffs[Bridge::FILE_DIFFS][] = $fileDiff;
            }
            $this->symfonyStyle->progressAdvance();
        }
        return $this->processedFileReporter->report($errorsAndDiffs, $configuration);
    }
    private function processFilePathWithPattern(string $filePath, Configuration $configuration) : ?FileDiff
    {
        $fixedContent = $this->markdownSnippetFormatter->format($filePath, $configuration);
        $originalFileContents = FileSystem::read($filePath);
        //$originalContent = $originalFileContents;
        if ($originalFileContents === $fixedContent) {
            // nothing has changed
            return null;
        }
        if (!$configuration->isFixer()) {
            return null;
        }
        $this->fileSystem->dumpFile($filePath, $fixedContent);
        $diff = $this->differ->diff($originalFileContents, $fixedContent);
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);
        $relativeFilePath = StaticRelativeFilePathHelper::resolveFromCwd($filePath);
        return new FileDiff(
            $relativeFilePath,
            $diff,
            $consoleFormattedDiff,
            // @todo
            []
        );
    }
}
