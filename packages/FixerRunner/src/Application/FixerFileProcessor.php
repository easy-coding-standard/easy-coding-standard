<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use Nette\Utils\FileSystem;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\Application\CurrentFileProvider;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\FileSystem\CachedFileLoader;
use Symplify\EasyCodingStandard\FixerRunner\Exception\Application\FixerFailedException;
use Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser;
use Symplify\EasyCodingStandard\Skipper;
use Throwable;
use function Safe\sprintf;
use function Safe\usort;

final class FixerFileProcessor implements FileProcessorInterface
{
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
     * @var bool
     */
    private $areFixersSorted = false;

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
     * @var CurrentFileProvider
     */
    private $currentFileProvider;

    public function __construct(
        ErrorAndDiffCollector $errorAndDiffCollector,
        Configuration $configuration,
        FileToTokensParser $fileToTokensParser,
        CachedFileLoader $cachedFileLoader,
        Skipper $skipper,
        DifferInterface $differ,
        CurrentFileProvider $currentFileProvider
    ) {
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
        $this->fileToTokensParser = $fileToTokensParser;
        $this->cachedFileLoader = $cachedFileLoader;
        $this->differ = $differ;
        $this->currentFileProvider = $currentFileProvider;
    }

    public function addFixer(FixerInterface $fixer): void
    {
        $this->fixers[] = $fixer;
    }

    /**
     * @return FixerInterface[]
     */
    public function getCheckers(): array
    {
        if (! $this->areFixersSorted) {
            $this->sortFixers();
        }

        return $this->fixers;
    }

    public function processFile(SplFileInfo $fileInfo): string
    {
        $this->currentFileProvider->setFileInfo($fileInfo);

        $oldContent = $this->cachedFileLoader->getFileContent($fileInfo);

        $tokens = $this->fileToTokensParser->parseFromFilePath($fileInfo->getRealPath());

        $appliedFixers = [];

        foreach ($this->getCheckers() as $fixer) {
            if ($this->shouldSkip($fileInfo, $fixer, $tokens)) {
                continue;
            }

            try {
                $fixer->fix($fileInfo, $tokens);
            } catch (Throwable $throwable) {
                throw new FixerFailedException(sprintf(
                    'Fixing of "%s" file by "%s" failed: %s in file %s on line %d',
                    $fileInfo->getPathname(),
                    get_class($fixer),
                    $throwable->getMessage(),
                    $throwable->getFile(),
                    $throwable->getLine()
                ), $throwable->getCode(), $throwable);
            }

            if (! $tokens->isChanged()) {
                continue;
            }

            $tokens->clearEmptyTokens();
            $tokens->clearChanged();
            $appliedFixers[] = get_class($fixer);
        }

        if (! $appliedFixers) {
            return $oldContent;
        }

        $diff = $this->differ->diff($oldContent, $tokens->generateCode());
        // some fixer with feature overlap can null each other
        if ($diff === '') {
            return $oldContent;
        }

        // file has changed
        $this->errorAndDiffCollector->addDiffForFileInfo($fileInfo, $diff, $appliedFixers);

        if ($this->configuration->isFixer()) {
            FileSystem::write($fileInfo->getRealPath(), $tokens->generateCode());
        }

        Tokens::clearCache();

        return $tokens->generateCode();
    }

    private function shouldSkip(SplFileInfo $file, FixerInterface $fixer, Tokens $tokens): bool
    {
        if ($this->skipper->shouldSkipCheckerAndFile($fixer, $file->getRealPath())) {
            return true;
        }

        return ! $fixer->supports($file) || ! $fixer->isCandidate($tokens);
    }

    private function sortFixers(): void
    {
        usort($this->fixers, function (FixerInterface $firstFixer, FixerInterface $secondFixer): bool {
            return $firstFixer->getPriority() < $secondFixer->getPriority();
        });

        $this->areFixersSorted = true;
    }
}
