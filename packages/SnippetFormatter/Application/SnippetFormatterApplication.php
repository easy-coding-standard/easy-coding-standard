<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Application;

use ECSPrefix20210604\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter;
use ECSPrefix20210604\Symplify\PackageBuilder\Console\ShellCode;
use ECSPrefix20210604\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20210604\Symplify\SmartFileSystem\SmartFileSystem;
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
    public function __construct(\Symplify\EasyCodingStandard\Configuration\Configuration $configuration, \Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter $snippetReporter, \Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter $snippetFormatter, \ECSPrefix20210604\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \ECSPrefix20210604\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter $processedFileReporter)
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
     */
    public function processFileInfosWithSnippetPattern(\Symplify\EasyCodingStandard\Configuration\Configuration $configuration, array $fileInfos, string $snippetPattern, string $kind) : int
    {
        $sources = $configuration->getSources();
        $fileCount = \count($fileInfos);
        if ($fileCount === 0) {
            $this->snippetReporter->reportNoFilesFound($sources);
            return \ECSPrefix20210604\Symplify\PackageBuilder\Console\ShellCode::SUCCESS;
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
     */
    private function processFileInfoWithPattern(\ECSPrefix20210604\Symplify\SmartFileSystem\SmartFileInfo $phpFileInfo, string $snippetPattern, string $kind)
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
