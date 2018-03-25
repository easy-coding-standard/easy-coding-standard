<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\FileSystem\CachedFileLoader;
use Symplify\EasyCodingStandard\FixerRunner\Exception\Application\FixerFailedException;
use Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser;
use Symplify\EasyCodingStandard\Performance\CheckerMetricRecorder;
use Symplify\EasyCodingStandard\Skipper;
use Throwable;

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
     * @var CheckerMetricRecorder
     */
    private $checkerMetricRecorder;

    /**
     * @var bool
     */
    private $areFixersSorted = false;

    /**
     * @var bool
     */
    private $isSecondRunPrepared = false;

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

    public function __construct(
        ErrorAndDiffCollector $errorAndDiffCollector,
        Configuration $configuration,
        CheckerMetricRecorder $checkerMetricRecorder,
        FileToTokensParser $fileToTokensParser,
        CachedFileLoader $cachedFileLoader,
        Skipper $skipper,
        DifferInterface $differ
    ) {
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
        $this->checkerMetricRecorder = $checkerMetricRecorder;
        $this->fileToTokensParser = $fileToTokensParser;
        $this->cachedFileLoader = $cachedFileLoader;
        $this->differ = $differ;
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
        $oldContent = $this->cachedFileLoader->getFileContent($fileInfo);

        $tokens = $this->fileToTokensParser->parseFromFilePath($fileInfo->getRealPath());

        $appliedFixers = [];

        foreach ($this->getCheckers() as $name => $fixer) {
            if ($this->shouldSkip($fileInfo, $fixer, $tokens)) {
                continue;
            }

            $this->checkerMetricRecorder->startWithChecker($fixer);

            try {
                $fixer->fix($fileInfo, $tokens);
            } catch (Throwable $throwable) {
                throw new FixerFailedException(sprintf(
                    'Fixing of "%s" file by "%s" failed: %s in file %s on line %d',
                    $fileInfo,
                    get_class($fixer),
                    $throwable->getMessage(),
                    $throwable->getFile(),
                    $throwable->getLine()
                ));
            }

            $this->checkerMetricRecorder->endWithChecker($fixer);

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

        $relativeFilePath = $fileInfo->getPath() . DIRECTORY_SEPARATOR . $fileInfo->getFilename();

        $this->errorAndDiffCollector->addDiffForFile($relativeFilePath, $diff, $appliedFixers);

        if ($this->configuration->isFixer()) {
            file_put_contents($fileInfo->getRealPath(), $tokens->generateCode());
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
