<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Application;

use Nette\Utils\FileSystem;
use PhpCsFixer\Differ\DifferInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\FileSystem\StaticRelativeFilePathHelper;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\MarkdownSnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;

final class MarkdownSnippetFormatterApplication
{
    public function __construct(
        private readonly SnippetReporter $snippetReporter,
        private readonly MarkdownSnippetFormatter $markdownSnippetFormatter,
        private readonly \Symfony\Component\Filesystem\Filesystem $fileSystem,
        private readonly SymfonyStyle $symfonyStyle,
        private readonly ProcessedFileReporter $processedFileReporter,
        private readonly DifferInterface $differ,
        private readonly ColorConsoleDiffFormatter $colorConsoleDiffFormatter
    ) {
    }

    /**
     * @param string[] $filePaths
     */
    public function processFileInfosWithSnippetPattern(Configuration $configuration, array $filePaths): int
    {
        $sources = $configuration->getSources();

        $fileCount = count($filePaths);
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

    private function processFilePathWithPattern(string $filePath, Configuration $configuration): ?FileDiff
    {
        $fixedContent = $this->markdownSnippetFormatter->format($filePath, $configuration);

        $originalFileContents = FileSystem::read($filePath);

        //$originalContent = $originalFileContents;
        if ($originalFileContents === $fixedContent) {
            // nothing has changed
            return null;
        }

        if (! $configuration->isFixer()) {
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
