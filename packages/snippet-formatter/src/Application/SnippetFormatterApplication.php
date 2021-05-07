<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Application;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class SnippetFormatterApplication
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var SnippetReporter
     */
    private $snippetReporter;

    /**
     * @var SnippetFormatter
     */
    private $snippetFormatter;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ProcessedFileReporter
     */
    private $processedFileReporter;

    public function __construct(
        Configuration $configuration,
        SnippetReporter $snippetReporter,
        SnippetFormatter $snippetFormatter,
        SmartFileSystem $smartFileSystem,
        SymfonyStyle $symfonyStyle,
        ProcessedFileReporter $processedFileReporter
    ) {
        $this->configuration = $configuration;
        $this->snippetReporter = $snippetReporter;
        $this->snippetFormatter = $snippetFormatter;
        $this->smartFileSystem = $smartFileSystem;
        $this->symfonyStyle = $symfonyStyle;
        $this->processedFileReporter = $processedFileReporter;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    public function processFileInfosWithSnippetPattern(
        Configuration $configuration,
        array $fileInfos,
        string $snippetPattern,
        string $kind
    ): int {
        $sources = $configuration->getSources();

        $fileCount = count($fileInfos);
        if ($fileCount === 0) {
            $this->snippetReporter->reportNoFilesFound($sources);
            return ShellCode::SUCCESS;
        }

        $this->symfonyStyle->progressStart($fileCount);
        foreach ($fileInfos as $fileInfo) {
            $this->processFileInfoWithPattern($fileInfo, $snippetPattern, $kind);
            $this->symfonyStyle->progressAdvance();
        }

        return $this->processedFileReporter->report($fileCount);
    }

    private function processFileInfoWithPattern(SmartFileInfo $phpFileInfo, string $snippetPattern, string $kind): void
    {
        $fixedContent = $this->snippetFormatter->format($phpFileInfo, $snippetPattern, $kind);
        if ($phpFileInfo->getContents() === $fixedContent) {
            // nothing has changed
            return;
        }

        if (! $this->configuration->isFixer()) {
            return;
        }

        $this->smartFileSystem->dumpFile($phpFileInfo->getPathname(), $fixedContent);
    }
}
