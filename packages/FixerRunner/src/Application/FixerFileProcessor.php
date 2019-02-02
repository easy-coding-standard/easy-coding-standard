<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use Nette\Utils\FileSystem;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\FileSystem\CachedFileLoader;
use Symplify\EasyCodingStandard\FixerRunner\Exception\Application\FixerFailedException;
use Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Throwable;

final class FixerFileProcessor implements FileProcessorInterface
{
    /**
     * @var string[]
     */
    private $appliedFixers = [];

    /**
     * @var FixerInterface[]
     */
    private $fixers = [];

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var FileToTokensParser
     */
    private $fileToTokensParser;

    /**
     * @var CachedFileLoader
     */
    private $cachedFileLoader;

    /**
     * @var DifferInterface
     */
    private $differ;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @param FixerInterface[] $fixers
     */
    public function __construct(
        ErrorAndDiffCollector $errorAndDiffCollector,
        Configuration $configuration,
        FileToTokensParser $fileToTokensParser,
        CachedFileLoader $cachedFileLoader,
        Skipper $skipper,
        DifferInterface $differ,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        array $fixers
    ) {
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
        $this->fileToTokensParser = $fileToTokensParser;
        $this->cachedFileLoader = $cachedFileLoader;
        $this->differ = $differ;
        $this->fixers = $this->sortFixers($fixers);
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    /**
     * @return FixerInterface[]
     */
    public function getCheckers(): array
    {
        return $this->fixers;
    }

    public function processFile(SmartFileInfo $smartFileInfo): string
    {
        $oldContent = $this->cachedFileLoader->getFileContent($smartFileInfo);

        $tokens = $this->fileToTokensParser->parseFromFilePath($smartFileInfo->getRealPath());

        $this->appliedFixers = [];
        foreach ($this->getCheckers() as $fixer) {
            $this->processTokensByFixer($smartFileInfo, $tokens, $fixer);
        }

        if ($this->appliedFixers === []) {
            return $oldContent;
        }

        $diff = $this->differ->diff($oldContent, $tokens->generateCode());
        // some fixer with feature overlap can null each other
        if ($diff === '') {
            return $oldContent;
        }

        // file has changed
        $this->errorAndDiffCollector->addDiffForFileInfo($smartFileInfo, $diff, $this->appliedFixers);

        if ($this->configuration->isFixer()) {
            FileSystem::write($smartFileInfo->getRealPath(), $tokens->generateCode());
        }

        Tokens::clearCache();

        return $tokens->generateCode();
    }

    /**
     * @param FixerInterface[] $fixers
     * @return FixerInterface[]
     */
    private function sortFixers(array $fixers): array
    {
        usort($fixers, function (FixerInterface $firstFixer, FixerInterface $secondFixer): int {
            return $secondFixer->getPriority() <=> $firstFixer->getPriority();
        });

        return $fixers;
    }

    private function processTokensByFixer(SmartFileInfo $smartFileInfo, Tokens $tokens, FixerInterface $fixer): void
    {
        if ($this->shouldSkip($smartFileInfo, $fixer, $tokens)) {
            return;
        }

        // show current fixer in --debug / -vvv
        if ($this->easyCodingStandardStyle->isDebug()) {
            $this->easyCodingStandardStyle->writeln(get_class($fixer));
        }

        try {
            $fixer->fix($smartFileInfo, $tokens);
        } catch (Throwable $throwable) {
            throw new FixerFailedException(sprintf(
                'Fixing of "%s" file by "%s" failed: %s in file %s on line %d',
                $smartFileInfo->getRelativeFilePath(),
                get_class($fixer),
                $throwable->getMessage(),
                $throwable->getFile(),
                $throwable->getLine()
            ), $throwable->getCode(), $throwable);
        }

        if (! $tokens->isChanged()) {
            return;
        }

        $tokens->clearEmptyTokens();
        $tokens->clearChanged();

        $this->appliedFixers[] = get_class($fixer);
    }

    private function shouldSkip(SmartFileInfo $smartFileInfo, FixerInterface $fixer, Tokens $tokens): bool
    {
        if ($this->skipper->shouldSkipCheckerAndFile($fixer, $smartFileInfo)) {
            return true;
        }

        return ! $fixer->supports($smartFileInfo) || ! $fixer->isCandidate($tokens);
    }
}
