<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use function Safe\getcwd;

final class ErrorAndDiffCollector
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

    /**
     * @var ErrorSorter
     */
    private $errorSorter;

    /**
     * @var FileDiffFactory
     */
    private $fileDiffFactory;

    /**
     * @var ErrorFactory
     */
    private $errorFactory;

    public function __construct(
        ChangedFilesDetector $changedFilesDetector,
        ErrorSorter $errorSorter,
        FileDiffFactory $fileDiffFactory,
        ErrorFactory $errorFactory
    ) {
        $this->changedFilesDetector = $changedFilesDetector;
        $this->errorSorter = $errorSorter;
        $this->fileDiffFactory = $fileDiffFactory;
        $this->errorFactory = $errorFactory;
    }

    public function addErrorMessage(SplFileInfo $fileInfo, int $line, string $message, string $sourceClass): void
    {
        $this->changedFilesDetector->invalidateFileInfo($fileInfo);

        $relativePathnameToRoot = Strings::substring($fileInfo->getRealPath(), strlen(getcwd()) + 1);
        $this->errors[$relativePathnameToRoot][] = $this->errorFactory->createFromLineMessageSourceClass(
            $line,
            $message,
            $sourceClass
        );
    }

    /**
     * @return Error[][]
     */
    public function getErrors(): array
    {
        return $this->errorSorter->sortByFileAndLine($this->errors);
    }

    public function getErrorCount(): int
    {
        return count(Arrays::flatten($this->errors));
    }

    /**
     * @param string[] $appliedCheckers
     */
    public function addDiffForFileInfo(SplFileInfo $fileInfo, string $diff, array $appliedCheckers): void
    {
        $this->changedFilesDetector->invalidateFileInfo($fileInfo);

        $this->fileDiffs[$fileInfo->getPathname()][] = $this->fileDiffFactory->createFromDiffAndAppliedCheckers(
            $diff,
            $appliedCheckers
        );
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

    /**
     * Used by external sniff/fixer testing classes
     */
    public function resetCounters(): void
    {
        $this->errors = [];
        $this->fileDiffs = [];
    }
}
