<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use ECSPrefix202301\Nette\Utils\FileSystem;
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
use SplFileInfo;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\FileDiffFactory;
use Symplify\EasyCodingStandard\FileSystem\TargetFileInfoResolver;
use Symplify\EasyCodingStandard\FixerRunner\Exception\Application\FixerFailedException;
use Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Skipper\Skipper\Skipper;
use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFilePathProvider;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
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
     * @readonly
     * @var \Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser
     */
    private $fileToTokensParser;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Skipper\Skipper\Skipper
     */
    private $skipper;
    /**
     * @readonly
     * @var \PhpCsFixer\Differ\DifferInterface
     */
    private $differ;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;
    /**
     * @readonly
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFilePathProvider
     */
    private $currentParentFilePathProvider;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\FileSystem\TargetFileInfoResolver
     */
    private $targetFileInfoResolver;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Error\FileDiffFactory
     */
    private $fileDiffFactory;
    /**
     * @param FixerInterface[] $fixers
     */
    public function __construct(FileToTokensParser $fileToTokensParser, Skipper $skipper, DifferInterface $differ, EasyCodingStandardStyle $easyCodingStandardStyle, \ECSPrefix202301\Symfony\Component\Filesystem\Filesystem $filesystem, CurrentParentFilePathProvider $currentParentFilePathProvider, TargetFileInfoResolver $targetFileInfoResolver, FileDiffFactory $fileDiffFactory, array $fixers)
    {
        $this->fileToTokensParser = $fileToTokensParser;
        $this->skipper = $skipper;
        $this->differ = $differ;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->filesystem = $filesystem;
        $this->currentParentFilePathProvider = $currentParentFilePathProvider;
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
    public function processFile(string $filePath, Configuration $configuration) : array
    {
        $tokens = $this->fileToTokensParser->parseFromFilePath($filePath);
        $appliedFixers = [];
        foreach ($this->fixers as $fixer) {
            if ($this->shouldSkipForMarkdownHeredocCheck($fixer)) {
                continue;
            }
            if ($this->processTokensByFixer($filePath, $tokens, $fixer)) {
                $appliedFixers[] = \get_class($fixer);
            }
        }
        if ($appliedFixers === []) {
            return [];
        }
        $fileContents = FileSystem::read($filePath);
        $diff = $this->differ->diff($fileContents, $tokens->generateCode());
        // some fixer with feature overlap can null each other
        if ($diff === '') {
            return [];
        }
        $fileDiffs = [];
        // file has changed
        $targetFilePath = $this->targetFileInfoResolver->resolveTargetFilePath($filePath);
        $fileDiffs[] = $this->fileDiffFactory->createFromDiffAndAppliedCheckers($targetFilePath, $diff, $appliedFixers);
        $tokenGeneratedCode = $tokens->generateCode();
        if ($configuration->isFixer()) {
            $this->filesystem->dumpFile($filePath, $tokenGeneratedCode);
        }
        Tokens::clearCache();
        return [Bridge::FILE_DIFFS => $fileDiffs];
    }
    public function processFileToString(string $filePath) : string
    {
        $tokens = $this->fileToTokensParser->parseFromFilePath($filePath);
        $appliedFixers = [];
        foreach ($this->fixers as $fixer) {
            if ($this->shouldSkipForMarkdownHeredocCheck($fixer)) {
                continue;
            }
            if ($this->processTokensByFixer($filePath, $tokens, $fixer)) {
                $appliedFixers[] = \get_class($fixer);
            }
        }
        $contents = FileSystem::read($filePath);
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
        \usort($fixers, static function (FixerInterface $firstFixer, FixerInterface $secondFixer) : int {
            return $secondFixer->getPriority() <=> $firstFixer->getPriority();
        });
        return $fixers;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return bool If fixer applied
     */
    private function processTokensByFixer(string $filePath, Tokens $tokens, FixerInterface $fixer) : bool
    {
        if ($this->shouldSkip($filePath, $fixer, $tokens)) {
            return \false;
        }
        // show current fixer in --debug / -vvv
        if ($this->easyCodingStandardStyle->isDebug()) {
            $this->easyCodingStandardStyle->writeln('     [fixer] ' . \get_class($fixer));
        }
        try {
            $fixer->fix(new SplFileInfo($filePath), $tokens);
        } catch (Throwable $throwable) {
            throw new FixerFailedException(\sprintf('Fixing of "%s" file by "%s" failed: %s in file %s on line %d', $filePath, \get_class($fixer), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()), $throwable->getCode(), $throwable);
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
    private function shouldSkip(string $filePath, FixerInterface $fixer, Tokens $tokens) : bool
    {
        if ($this->skipper->shouldSkipElementAndFilePath($fixer, $filePath)) {
            return \true;
        }
        if (!$fixer->supports(new SplFileInfo($filePath))) {
            return \true;
        }
        return !$fixer->isCandidate($tokens);
    }
    /**
     * Is markdown doc checker → skip useless rules
     */
    private function shouldSkipForMarkdownHeredocCheck(FixerInterface $fixer) : bool
    {
        if ($this->currentParentFilePathProvider->provide() === null) {
            return \false;
        }
        foreach (self::MARKDOWN_EXCLUDED_FIXERS as $markdownExcludedFixer) {
            if ($fixer instanceof $markdownExcludedFixer) {
                return \true;
            }
        }
        return \false;
    }
}
