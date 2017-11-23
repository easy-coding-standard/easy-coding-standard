<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\FixerRunner\ChangedLinesDetector;
use Symplify\EasyCodingStandard\FixerRunner\Exception\Application\FixerFailedException;
use Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser;
use Symplify\EasyCodingStandard\Performance\CheckerMetricRecorder;
use Symplify\EasyCodingStandard\Skipper;
use Throwable;

final class FixerFileProcessor implements FileProcessorInterface
{
    /**
     * @var FixerInterface[]|DualRunInterface[]
     */
    private $fixers = [];

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ChangedLinesDetector
     */
    private $changedLinesDetector;

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

    public function __construct(
        ErrorCollector $errorCollector,
        Skipper $skipper,
        Configuration $configuration,
        ChangedLinesDetector $changedLinesDetector,
        CheckerMetricRecorder $checkerMetricRecorder,
        FileToTokensParser $fileToTokensParser
    ) {
        $this->errorCollector = $errorCollector;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
        $this->changedLinesDetector = $changedLinesDetector;
        $this->checkerMetricRecorder = $checkerMetricRecorder;
        $this->fileToTokensParser = $fileToTokensParser;
    }

    public function addFixer(FixerInterface $fixer): void
    {
        $this->fixers[] = $fixer;
    }

    /**
     * @return FixerInterface[]|DualRunInterface[]
     */
    public function getFixers(): array
    {
        if (! $this->areFixersSorted) {
            $this->sortFixers();
        }

        return $this->fixers;
    }

    public function processFile(SplFileInfo $file): void
    {
        $oldContent = file_get_contents($file->getRealPath());

        $tokens = $this->fileToTokensParser->parseFromFilePath($file->getRealPath());

        $appliedFixers = [];
        $latestContent = $oldContent;

        foreach ($this->getFixers() as $name => $fixer) {
            if ($this->shouldSkip($file, $fixer, $tokens)) {
                continue;
            }

            $this->checkerMetricRecorder->startWithChecker($fixer);

            try {
                $fixer->fix($file, $tokens);
            } catch (Throwable $throwable) {
                throw new FixerFailedException(sprintf(
                    'Fixing of "%s" file by "%s" failed: %s in file %s on line %d',
                    $file,
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

            $changedLines = $this->changedLinesDetector->detectInBeforeAfter($latestContent, $tokens->generateCode());
            $latestContent = $tokens->generateCode();

            foreach ($changedLines as $changedLine) {
                $this->addErrorToErrorMessageCollector($file, $fixer, $changedLine);
            }

            $tokens->clearEmptyTokens();
            $tokens->clearChanged();
            $appliedFixers[] = $fixer->getName();
        }

        if (! $appliedFixers) {
            $this->fileToTokensParser->clearCache();
            return;
        }

        if ($this->configuration->isFixer() && $oldContent !== $tokens->getCodeHash()) {
            file_put_contents($file->getRealPath(), $tokens->generateCode());
        }

        Tokens::clearCache();
    }

    public function processFileSecondRun(SplFileInfo $file): void
    {
        $this->prepareSecondRun();
        $this->processFile($file);
    }

    /**
     * @return DualRunInterface[]
     */
    public function getDualRunFixers(): array
    {
        return array_filter($this->fixers, function (FixerInterface $fixer) {
            return $fixer instanceof DualRunInterface;
        });
    }

    private function addErrorToErrorMessageCollector(SplFileInfo $file, FixerInterface $fixer, int $line): void
    {
        $filePath = str_replace('//', '/', $file->getPathname());

        $this->errorCollector->addErrorMessage(
            $filePath,
            $line,
            $this->prepareErrorMessageFromFixer($fixer),
            get_class($fixer),
            true
        );
    }

    private function prepareErrorMessageFromFixer(FixerInterface $fixer): string
    {
        if ($fixer instanceof DefinedFixerInterface) {
            return $fixer->getDefinition()
                ->getSummary();
        }

        return $fixer->getName();
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
        usort($this->fixers, function (FixerInterface $firstFixer, FixerInterface $secondFixer) {
            return $firstFixer->getPriority() < $secondFixer->getPriority();
        });

        $this->areFixersSorted = true;
    }

    private function prepareSecondRun(): void
    {
        if ($this->isSecondRunPrepared) {
            return;
        }

        $this->fixers = $this->getDualRunFixers();
        foreach ($this->fixers as $fixer) {
            $fixer->increaseRun();
        }

        $this->isSecondRunPrepared = true;
    }
}
