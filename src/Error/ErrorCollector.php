<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Arrays;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Contract\ChangedFilesDetectorInterface;

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
    private $errorMessageSorter;

    /**
     * @var ChangedFilesDetectorInterface
     */
    private $changedFilesDetector;

    public function __construct(ErrorSorter $errorMessageSorter, ChangedFilesDetectorInterface $changedFilesDetector)
    {
        $this->errorMessageSorter = $errorMessageSorter;
        $this->changedFilesDetector = $changedFilesDetector;
    }

    public function addErrorMessage(
        string $filePath,
        int $line,
        string $message,
        string $sourceClass,
        bool $isFixable
    ): void {
        $error = new Error($line, $message, $sourceClass, $isFixable);

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
        return $this->errorMessageSorter->sortByFileAndLine($this->unfixableErrors);
    }

    /**
     * @return Error[][]
     */
    private function getFixableErrors(): array
    {
        return $this->errorMessageSorter->sortByFileAndLine($this->fixableErrors);
    }
}
