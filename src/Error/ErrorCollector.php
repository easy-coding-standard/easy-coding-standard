<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Arrays;
use PhpParser\Node\Expr\ArrayDimFetch;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;

final class ErrorCollector
{
    /**
     * @var Error[][]
     */
    private $errors = [];

    /**
     * @var ErrorSorter
     */
    private $errorSorter;

    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    /**
     * @var mixed[]
     */
    private $fileDiffs = [];

    public function __construct(ErrorSorter $errorSorter, ChangedFilesDetector $changedFilesDetector)
    {
        $this->errorSorter = $errorSorter;
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
        return $this->errorSorter->sortByFileAndLine($this->errors);
    }

    /**
     * @param string[] $appliedCheckers
     */
    public function addDiffForFile(string $filePath, string $diff, array $appliedCheckers): void
    {
        // @todo use value object
        $this->fileDiffs[$filePath][] = [
            'diff' => $diff,
            'appliedCheckers' => $appliedCheckers
        ];
    }

    /**
     * @return mixed[]
     */
    public function getFileDiffs(): array
    {
        return $this->fileDiffs;
    }
}
