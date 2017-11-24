<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Arrays;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;

final class ErrorCollector
{
    /**
     * @var Error[][]
     */
    private $fixableErrors = [];

    /**
     * @var Error[][]
     */
    private $unfixableErrors = [];

    /**
     * @var ErrorSorter
     */
    private $errorSorter;

    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    /**
     * @var string[]
     */
    private $changedFilesDiffs = [];

    public function __construct(ErrorSorter $errorSorter, ChangedFilesDetector $changedFilesDetector)
    {
        $this->errorSorter = $errorSorter;
        $this->changedFilesDetector = $changedFilesDetector;
    }

    public function addErrorMessage(
        string $filePath,
        int $line,
        string $message,
        string $sourceClass,
        bool $isFixable
    ): void {
        $error = Error::createFromLineMessageSourceClassAndFixable($line, $message, $sourceClass, $isFixable);

        if ($isFixable) {
            $this->fixableErrors[$filePath][] = $error;
        } else {
            $this->unfixableErrors[$filePath][] = $error;
        }

        $this->changedFilesDetector->invalidateFile($filePath);
    }

    public function getErrorCount(): int
    {
        return $this->getFixableErrorCount() + $this->getUnfixableErrorCount();
    }

    public function getFixableErrorCount(): int
    {
        return count(Arrays::flatten($this->fixableErrors));
    }

    public function getUnfixableErrorCount(): int
    {
        return count(Arrays::flatten($this->unfixableErrors));
    }

    public function resetCounters(): void
    {
        $this->fixableErrors = [];
        $this->unfixableErrors = [];
    }

    /**
     * @return Error[][]
     */
    public function getAllErrors(): array
    {
        $unfixableErrors = $this->getUnfixableErrors();
        $fixableErrors = $this->getFixableErrors();

        $allErrors = $unfixableErrors;

        foreach ($fixableErrors as $file => $fixableError) {
            $allErrors[$file] = array_merge($unfixableErrors[$file] ?? [], $fixableError);
        }

        return $allErrors;
    }

    /**
     * @return Error[][]
     */
    public function getUnfixableErrors(): array
    {
        return $this->errorSorter->sortByFileAndLine($this->unfixableErrors);
    }

    /**
     * @return Error[][]
     */
    private function getFixableErrors(): array
    {
        return $this->errorSorter->sortByFileAndLine($this->fixableErrors);
    }

    public function addFixerDiffForFile(string $filePath, string $diff): void
    {
        $this->changedFilesDiffs[$filePath] = $diff;
    }
}
