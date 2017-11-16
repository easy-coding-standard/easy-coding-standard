<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symfony\Component\Filesystem\Exception\IOException;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\FixerRunner\ChangedLinesDetector;
use Symplify\EasyCodingStandard\FixerRunner\Exception\Application\FixerFailedException;
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

    public function __construct(
        ErrorCollector $errorCollector,
        Skipper $skipper,
        Configuration $configuration,
        ChangedLinesDetector $changedLinesDetector,
        CheckerMetricRecorder $checkerMetricRecorder
    ) {
        $this->errorCollector = $errorCollector;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
        $this->changedLinesDetector = $changedLinesDetector;
        $this->checkerMetricRecorder = $checkerMetricRecorder;
    }

    public function addFixer(FixerInterface $fixer): void
    {
        $this->fixers[] = $fixer;
    }

    /**
     * @return FixerInterface[]
     */
    public function getFixers(): array
    {
        $this->sortFixers();

        return $this->fixers;
    }

    public function processFile(SplFileInfo $file): void
    {
        $oldContent = file_get_contents($file->getRealPath());
        $tokens = Tokens::fromCode($oldContent);
        $oldHash = $tokens->getCodeHash();

        $appliedFixers = [];
        $latestContent = $oldContent;

        foreach ($this->getFixers() as $fixer) {
            if ($this->skipper->shouldSkipCheckerAndFile($fixer, $file->getRealPath())) {
                continue;
            }

            if (! $fixer->supports($file) || ! $fixer->isCandidate($tokens)) {
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
            } finally {
                $this->checkerMetricRecorder->endWithChecker($fixer);
            }

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
            Tokens::clearCache();
            return;
        }

        $newContent = $tokens->generateCode();
        $newHash = $tokens->getCodeHash();

        // We need to check if content was changed and then applied changes.
        // But we can't simple check $appliedFixers, because one fixer may revert
        // work of other and both of them will mark collection as changed.
        // Therefore we need to check if code hashes changed.
        if ($this->configuration->isFixer() && ($oldHash !== $newHash)) {
            if (@file_put_contents($file->getRealPath(), $newContent) === false) {
                // @todo: move to sniffer FixerFileProcessor as well, decouple FileSystem service?
                $error = error_get_last();

                throw new IOException(
                    sprintf(
                        'Failed to write file "%s", "%s".',
                        $file->getPathname(),
                        $error ? $error['message'] : 'no reason available'
                    ),
                    0,
                    null,
                    $file->getRealPath()
                );
            }
        }

        Tokens::clearCache();
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
            $definition = $fixer->getDefinition();

            return $definition->getSummary();
        }

        return $fixer->getName();
    }

    private function sortFixers(): void
    {
        usort($this->fixers, function (FixerInterface $firstFixer, FixerInterface $secondFixer) {
            return $firstFixer->getPriority() < $secondFixer->getPriority();
        });
    }
}
