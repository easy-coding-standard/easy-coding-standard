<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Arrays;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;

final class ErrorCollector
{
    /**
     * @var Error[][]
     */
    private $errors = [];

    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    /**
     * @var FileDiff[][]
     */
    private $fileDiffs = [];

    public function __construct(ChangedFilesDetector $changedFilesDetector)
    {
        $this->changedFilesDetector = $changedFilesDetector;
    }

    public function addErrorMessage(string $filePath, int $line, string $message, string $sourceClass): void
    {
        $this->errors[$filePath][] = Error::createFromLineMessageSourceClass($line, $message, $sourceClass);

        $this->changedFilesDetector->invalidateFile($filePath);
    }

    public function getErrorCount(): int
    {
        return $this->getFixableErrorCount() + $this->getUnfixableErrorCount();
    }

    public function getFixableErrorCount(): int
    {
        return count(Arrays::flatten($this->getFileDiffs()));
    }

    public function getUnfixableErrorCount(): int
    {
        return count(Arrays::flatten($this->errors));
    }

    public function resetCounters(): void
    {
        $this->errors = [];
        $this->fileDiffs = [];
    }

    /**
     * @return Error[][]
     */
    public function getAllErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string[] $appliedCheckers
     */
    public function addDiffForFile(string $filePath, string $diff, array $appliedCheckers): void
    {
        $this->changedFilesDetector->invalidateFile($filePath);

        $this->fileDiffs[$filePath][] = FileDiff::createFromDiffAndAppliedCheckers($diff, $appliedCheckers);
    }

    /**
     * @return FileDiff[][]
     */
    public function getFileDiffs(): array
    {
        return $this->fileDiffs;
    }
}
