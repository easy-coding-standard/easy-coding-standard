<?php

namespace Symplify\EasyCodingStandard\SnippetFormatter\Application;

use ECSPrefix20210507\Symfony\Component\Console\Style\SymfonyStyle;
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
    /**
     * @param \Symplify\EasyCodingStandard\Configuration\Configuration $configuration
     * @param \Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter $snippetReporter
     * @param \Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter $snippetFormatter
     * @param \Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem
     * @param \ECSPrefix20210507\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter $processedFileReporter
     */
    public function __construct($configuration, $snippetReporter, $snippetFormatter, $smartFileSystem, $symfonyStyle, $processedFileReporter)
    {
        $this->configuration = $configuration;
        $this->snippetReporter = $snippetReporter;
        $this->snippetFormatter = $snippetFormatter;
        $this->smartFileSystem = $smartFileSystem;
        $this->symfonyStyle = $symfonyStyle;
        $this->processedFileReporter = $processedFileReporter;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @param \Symplify\EasyCodingStandard\Configuration\Configuration $configuration
     * @param string $snippetPattern
     * @param string $kind
     * @return int
     */
    public function processFileInfosWithSnippetPattern($configuration, array $fileInfos, $snippetPattern, $kind)
    {
        $sources = $configuration->getSources();
        $fileCount = \count($fileInfos);
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
    /**
     * @return void
     * @param \Symplify\SmartFileSystem\SmartFileInfo $phpFileInfo
     * @param string $snippetPattern
     * @param string $kind
     */
    private function processFileInfoWithPattern($phpFileInfo, $snippetPattern, $kind)
    {
        $fixedContent = $this->snippetFormatter->format($phpFileInfo, $snippetPattern, $kind);
        if ($phpFileInfo->getContents() === $fixedContent) {
            // nothing has changed
            return;
        }
        if (!$this->configuration->isFixer()) {
            return;
        }
        $this->smartFileSystem->dumpFile($phpFileInfo->getPathname(), $fixedContent);
    }
}
