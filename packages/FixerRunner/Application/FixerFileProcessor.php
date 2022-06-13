<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\FileDiffFactory;
use Symplify\EasyCodingStandard\FileSystem\TargetFileInfoResolver;
use Symplify\EasyCodingStandard\FixerRunner\Exception\Application\FixerFailedException;
use Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix202206\Symplify\Skipper\Skipper\Skipper;
use ECSPrefix202206\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix202206\Symplify\SmartFileSystem\SmartFileSystem;
use Throwable;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Error\ErrorCollector\FixerFileProcessorTest
 */
final class FixerFileProcessor implements FileProcessorInterface
{
    /**
     * @var array<class-string<FixerInterface>>
     */
    private const MARKDOWN_EXCLUDED_FIXERS = [VoidReturnFixer::class, DeclareStrictTypesFixer::class, SingleBlankLineBeforeNamespaceFixer::class, BlankLineAfterOpeningTagFixer::class, SingleBlankLineAtEofFixer::class, ProtectedToPrivateFixer::class];
    /**
     * @var FixerInterface[]
     */
    private $fixers = [];
    /**
     * @var \Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser
     */
    private $fileToTokensParser;
    /**
     * @var \Symplify\Skipper\Skipper\Skipper
     */
    private $skipper;
    /**
     * @var \PhpCsFixer\Differ\DifferInterface
     */
    private $differ;
    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider
     */
    private $currentParentFileInfoProvider;
    /**
     * @var \Symplify\EasyCodingStandard\FileSystem\TargetFileInfoResolver
     */
    private $targetFileInfoResolver;
    /**
     * @var \Symplify\EasyCodingStandard\Error\FileDiffFactory
     */
    private $fileDiffFactory;
    /**
     * @param FixerInterface[] $fixers
     */
    public function __construct(FileToTokensParser $fileToTokensParser, Skipper $skipper, DifferInterface $differ, EasyCodingStandardStyle $easyCodingStandardStyle, SmartFileSystem $smartFileSystem, CurrentParentFileInfoProvider $currentParentFileInfoProvider, TargetFileInfoResolver $targetFileInfoResolver, FileDiffFactory $fileDiffFactory, array $fixers)
    {
        $this->fileToTokensParser = $fileToTokensParser;
        $this->skipper = $skipper;
        $this->differ = $differ;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
        $this->targetFileInfoResolver = $targetFileInfoResolver;
        $this->fileDiffFactory = $fileDiffFactory;
        $this->fixers = $this->sortFixers($fixers);
    }
    /**
     * @return FixerInterface[]
     */
    public function getCheckers() : array
    {
        return $this->fixers;
    }
    /**
     * @return array{file_diffs?: FileDiff[]}
     */
    public function processFile(SmartFileInfo $smartFileInfo, Configuration $configuration) : array
    {
        $tokens = $this->fileToTokensParser->parseFromFilePath($smartFileInfo->getRealPath());
        $appliedFixers = [];
        foreach ($this->fixers as $fixer) {
            if ($this->shouldSkipForMarkdownHeredocCheck($fixer)) {
                continue;
            }
            if ($this->processTokensByFixer($smartFileInfo, $tokens, $fixer)) {
                $appliedFixers[] = \get_class($fixer);
            }
        }
        if ($appliedFixers === []) {
            return [];
        }
        $contents = $smartFileInfo->getContents();
        $diff = $this->differ->diff($contents, $tokens->generateCode());
        // some fixer with feature overlap can null each other
        if ($diff === '') {
            return [];
        }
        $fileDiffs = [];
        // file has changed
        $targetFileInfo = $this->targetFileInfoResolver->resolveTargetFileInfo($smartFileInfo);
        $fileDiffs[] = $this->fileDiffFactory->createFromDiffAndAppliedCheckers($targetFileInfo, $diff, $appliedFixers);
        $tokenGeneratedCode = $tokens->generateCode();
        if ($configuration->isFixer()) {
            $this->smartFileSystem->dumpFile($smartFileInfo->getRealPath(), $tokenGeneratedCode);
        }
        Tokens::clearCache();
        return [Bridge::FILE_DIFFS => $fileDiffs];
    }
    public function processFileToString(SmartFileInfo $smartFileInfo) : string
    {
        $tokens = $this->fileToTokensParser->parseFromFilePath($smartFileInfo->getRealPath());
        $appliedFixers = [];
        foreach ($this->fixers as $fixer) {
            if ($this->shouldSkipForMarkdownHeredocCheck($fixer)) {
                continue;
            }
            if ($this->processTokensByFixer($smartFileInfo, $tokens, $fixer)) {
                $appliedFixers[] = \get_class($fixer);
            }
        }
        $contents = $smartFileInfo->getContents();
        if ($appliedFixers === []) {
            return $contents;
        }
        $diff = $this->differ->diff($contents, $tokens->generateCode());
        // some fixer with feature overlap can null each other
        if ($diff === '') {
            return $contents;
        }
        return $tokens->generateCode();
    }
    /**
     * @param FixerInterface[] $fixers
     * @return FixerInterface[]
     */
    private function sortFixers(array $fixers) : array
    {
        \usort($fixers, function (FixerInterface $firstFixer, FixerInterface $secondFixer) : int {
            return $secondFixer->getPriority() <=> $firstFixer->getPriority();
        });
        return $fixers;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return bool If fixer applied
     */
    private function processTokensByFixer(SmartFileInfo $smartFileInfo, Tokens $tokens, FixerInterface $fixer) : bool
    {
        if ($this->shouldSkip($smartFileInfo, $fixer, $tokens)) {
            return \false;
        }
        // show current fixer in --debug / -vvv
        if ($this->easyCodingStandardStyle->isDebug()) {
            $this->easyCodingStandardStyle->writeln('     [fixer] ' . \get_class($fixer));
        }
        try {
            $fixer->fix($smartFileInfo, $tokens);
        } catch (Throwable $throwable) {
            throw new FixerFailedException(\sprintf('Fixing of "%s" file by "%s" failed: %s in file %s on line %d', $smartFileInfo->getRelativeFilePath(), \get_class($fixer), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()), $throwable->getCode(), $throwable);
        }
        if (!$tokens->isChanged()) {
            return \false;
        }
        $tokens->clearChanged();
        $tokens->clearEmptyTokens();
        return \true;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function shouldSkip(SmartFileInfo $smartFileInfo, FixerInterface $fixer, Tokens $tokens) : bool
    {
        if ($this->skipper->shouldSkipElementAndFileInfo($fixer, $smartFileInfo)) {
            return \true;
        }
        if (!$fixer->supports($smartFileInfo)) {
            return \true;
        }
        return !$fixer->isCandidate($tokens);
    }
    /**
     * Is markdown/herenow doc checker → skip useless rules
     */
    private function shouldSkipForMarkdownHeredocCheck(FixerInterface $fixer) : bool
    {
        if ($this->currentParentFileInfoProvider->provide() === null) {
            return \false;
        }
        foreach (self::MARKDOWN_EXCLUDED_FIXERS as $markdownExcludedFixer) {
            if (\is_a($fixer, $markdownExcludedFixer, \true)) {
                return \true;
            }
        }
        return \false;
    }
}
