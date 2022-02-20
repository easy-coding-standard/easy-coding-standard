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
use ECSPrefix20220220\Symplify\Skipper\Skipper\Skipper;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem;
use Throwable;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Error\ErrorCollector\FixerFileProcessorTest
 */
final class FixerFileProcessor implements \Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface
{
    /**
     * @var array<class-string<FixerInterface>>
     */
    private const MARKDOWN_EXCLUDED_FIXERS = [\PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer::class, \PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class, \PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer::class, \PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer::class, \PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer::class, \PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer::class];
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
    public function __construct(\Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser $fileToTokensParser, \ECSPrefix20220220\Symplify\Skipper\Skipper\Skipper $skipper, \PhpCsFixer\Differ\DifferInterface $differ, \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle, \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider $currentParentFileInfoProvider, \Symplify\EasyCodingStandard\FileSystem\TargetFileInfoResolver $targetFileInfoResolver, \Symplify\EasyCodingStandard\Error\FileDiffFactory $fileDiffFactory, array $fixers)
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
     * @return array<string, FileDiff[]>
     */
    public function processFile(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration) : array
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
        \PhpCsFixer\Tokenizer\Tokens::clearCache();
        return [\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS => $fileDiffs];
    }
    public function processFileToString(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : string
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
        \usort($fixers, function (\PhpCsFixer\Fixer\FixerInterface $firstFixer, \PhpCsFixer\Fixer\FixerInterface $secondFixer) : int {
            return $secondFixer->getPriority() <=> $firstFixer->getPriority();
        });
        return $fixers;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return bool If fixer applied
     */
    private function processTokensByFixer(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, \PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Fixer\FixerInterface $fixer) : bool
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
        } catch (\Throwable $throwable) {
            throw new \Symplify\EasyCodingStandard\FixerRunner\Exception\Application\FixerFailedException(\sprintf('Fixing of "%s" file by "%s" failed: %s in file %s on line %d', $smartFileInfo->getRelativeFilePath(), \get_class($fixer), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()), $throwable->getCode(), $throwable);
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
    private function shouldSkip(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, \PhpCsFixer\Fixer\FixerInterface $fixer, \PhpCsFixer\Tokenizer\Tokens $tokens) : bool
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
    private function shouldSkipForMarkdownHeredocCheck(\PhpCsFixer\Fixer\FixerInterface $fixer) : bool
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
