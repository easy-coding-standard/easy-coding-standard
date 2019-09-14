<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Arrays;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class ErrorAndDiffCollector
{
    /**
     * @var Error[][]
     */
    private $errors = [];

    /**
     * @var FileDiff[][]
     */
    private $fileDiffs = [];

    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

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

    public function addErrorMessage(SmartFileInfo $fileInfo, int $line, string $message, string $sourceClass): void
    {
        $this->changedFilesDetector->invalidateFileInfo($fileInfo);

        $relativePathnameToRoot = $fileInfo->getRelativeFilePathFromDirectory(getcwd());

        $error = $this->errorFactory->create($line, $message, $sourceClass, $fileInfo);

        $this->errors[$relativePathnameToRoot][] = $error;
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
    public function addDiffForFileInfo(SmartFileInfo $smartFileInfo, string $diff, array $appliedCheckers): void
    {
        $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);

        $this->fileDiffs[$smartFileInfo->getRelativeFilePath()][] = $this->fileDiffFactory->createFromDiffAndAppliedCheckers(
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
