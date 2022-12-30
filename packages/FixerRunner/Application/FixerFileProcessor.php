<?php

declare(strict_types=1);

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
use Symplify\EasyCodingStandard\Skipper\Skipper\Skipper;
use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;
use Throwable;

/**
 * @see \Symplify\EasyCodingStandard\Tests\Error\ErrorCollector\FixerFileProcessorTest
 */
final class FixerFileProcessor implements FileProcessorInterface
{
    /**
     * @var array<class-string<FixerInterface>>
     */
    private const MARKDOWN_EXCLUDED_FIXERS = [
        VoidReturnFixer::class,
        DeclareStrictTypesFixer::class,
        SingleBlankLineBeforeNamespaceFixer::class,
        BlankLineAfterOpeningTagFixer::class,
        SingleBlankLineAtEofFixer::class,
        ProtectedToPrivateFixer::class,
    ];

    /**
     * @var FixerInterface[]
     */
    private array $fixers = [];

    /**
     * @param FixerInterface[] $fixers
     */
    public function __construct(
        private FileToTokensParser $fileToTokensParser,
        private Skipper $skipper,
        private DifferInterface $differ,
        private EasyCodingStandardStyle $easyCodingStandardStyle,
        private SmartFileSystem $smartFileSystem,
        private CurrentParentFileInfoProvider $currentParentFileInfoProvider,
        private TargetFileInfoResolver $targetFileInfoResolver,
        private FileDiffFactory $fileDiffFactory,
        array $fixers
    ) {
        $this->fixers = $this->sortFixers($fixers);
    }

    /**
     * @return FixerInterface[]
     */
    public function getCheckers(): array
    {
        return $this->fixers;
    }

    /**
     * @return array{file_diffs?: FileDiff[]}
     */
    public function processFile(SmartFileInfo $smartFileInfo, Configuration $configuration): array
    {
        $tokens = $this->fileToTokensParser->parseFromFilePath($smartFileInfo->getRealPath());

        $appliedFixers = [];

        foreach ($this->fixers as $fixer) {
            if ($this->shouldSkipForMarkdownHeredocCheck($fixer)) {
                continue;
            }

            if ($this->processTokensByFixer($smartFileInfo, $tokens, $fixer)) {
                $appliedFixers[] = $fixer::class;
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
        $fileDiffs[] = $this->fileDiffFactory->createFromDiffAndAppliedCheckers(
            $targetFileInfo,
            $diff,
            $appliedFixers
        );

        $tokenGeneratedCode = $tokens->generateCode();
        if ($configuration->isFixer()) {
            $this->smartFileSystem->dumpFile($smartFileInfo->getRealPath(), $tokenGeneratedCode);
        }

        Tokens::clearCache();

        return [
            Bridge::FILE_DIFFS => $fileDiffs,
        ];
    }

    public function processFileToString(SmartFileInfo $smartFileInfo): string
    {
        $tokens = $this->fileToTokensParser->parseFromFilePath($smartFileInfo->getRealPath());

        $appliedFixers = [];
        foreach ($this->fixers as $fixer) {
            if ($this->shouldSkipForMarkdownHeredocCheck($fixer)) {
                continue;
            }

            if ($this->processTokensByFixer($smartFileInfo, $tokens, $fixer)) {
                $appliedFixers[] = $fixer::class;
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
    private function sortFixers(array $fixers): array
    {
        usort(
            $fixers,
            static fn (FixerInterface $firstFixer, FixerInterface $secondFixer): int => $secondFixer->getPriority() <=> $firstFixer->getPriority()
        );

        return $fixers;
    }

    /**
     * @param Tokens<Token> $tokens
     * @return bool If fixer applied
     */
    private function processTokensByFixer(SmartFileInfo $smartFileInfo, Tokens $tokens, FixerInterface $fixer): bool
    {
        if ($this->shouldSkip($smartFileInfo, $fixer, $tokens)) {
            return false;
        }

        // show current fixer in --debug / -vvv
        if ($this->easyCodingStandardStyle->isDebug()) {
            $this->easyCodingStandardStyle->writeln('     [fixer] ' . $fixer::class);
        }

        try {
            $fixer->fix($smartFileInfo, $tokens);
        } catch (Throwable $throwable) {
            throw new FixerFailedException(sprintf(
                'Fixing of "%s" file by "%s" failed: %s in file %s on line %d',
                $smartFileInfo->getRelativeFilePath(),
                $fixer::class,
                $throwable->getMessage(),
                $throwable->getFile(),
                $throwable->getLine()
            ), $throwable->getCode(), $throwable);
        }

        if (! $tokens->isChanged()) {
            return false;
        }

        $tokens->clearChanged();
        $tokens->clearEmptyTokens();

        return true;
    }

    /**
     * @param Tokens<Token> $tokens
     */
    private function shouldSkip(SmartFileInfo $smartFileInfo, FixerInterface $fixer, Tokens $tokens): bool
    {
        if ($this->skipper->shouldSkipElementAndFileInfo($fixer, $smartFileInfo)) {
            return true;
        }

        if (! $fixer->supports($smartFileInfo)) {
            return true;
        }

        return ! $fixer->isCandidate($tokens);
    }

    /**
     * Is markdown/herenow doc checker → skip useless rules
     */
    private function shouldSkipForMarkdownHeredocCheck(FixerInterface $fixer): bool
    {
        if ($this->currentParentFileInfoProvider->provide() === null) {
            return false;
        }

        foreach (self::MARKDOWN_EXCLUDED_FIXERS as $markdownExcludedFixer) {
            if (is_a($fixer, $markdownExcludedFixer, true)) {
                return true;
            }
        }

        return false;
    }
}
