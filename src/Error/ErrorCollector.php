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

    /**
     * @return Error[][]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorCount(): int
    {
        return count(Arrays::flatten($this->errors))
    }

    /**
     * @param string[] $appliedCheckers
     */
    public function addDiffForFile(string $filePath, string $diff, array $appliedCheckers): void
    {
        $this->changedFilesDetector->invalidateFile($filePath);

        $this->fileDiffs[$filePath][] = FileDiff::createFromDiffAndAppliedCheckers($diff, $appliedCheckers);
    }

    public function getFileDiffsCount(): int
    {
        return count(Arrays::flatten($this->getFileDiffs()));
    }

    /**
     * @return FileDiff[][]
     */
    public function getFileDiffs(): array
    {
        return $this->fileDiffs;
    }

    public function resetCounters(): void
    {
        $this->errors = [];
        $this->fileDiffs = [];
    }
}
