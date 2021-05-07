<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject\Error;

final class ErrorAndDiffResult
{
    /**
     * @var CodingStandardError[]
     */
    private $codingStandardErrors = [];

    /**
     * @var FileDiff[]
     */
    private $fileDiffs = [];

    /**
     * @var SystemError[]
     */
    private $systemErrors = [];

    /**
     * @param CodingStandardError[] $codingStandardErrors
     * @param FileDiff[] $fileDiffs
     * @param SystemError[] $systemErrors
     */
    public function __construct(array $codingStandardErrors, array $fileDiffs, array $systemErrors)
    {
        $this->codingStandardErrors = $this->sortByFileAndLine($codingStandardErrors);
        $this->fileDiffs = $this->sortByFilePath($fileDiffs);
        $this->systemErrors = $systemErrors;
    }

    public function getErrorCount(): int
    {
        return count($this->codingStandardErrors) + count($this->systemErrors);
    }

    public function getFileDiffsCount(): int
    {
        return count($this->fileDiffs);
    }

    /**
     * @return CodingStandardError[]
     */
    public function getErrors(): array
    {
        return $this->codingStandardErrors;
    }

    /**
     * @return SystemError[]
     */
    public function getSystemErrors(): array
    {
        return $this->systemErrors;
    }

    /**
     * @return FileDiff[]
     */
    public function getFileDiffs(): array
    {
        return $this->fileDiffs;
    }

    /**
     * @param CodingStandardError[] $errorMessages
     * @return CodingStandardError[]
     */
    private function sortByFileAndLine(array $errorMessages): array
    {
        usort(
            $errorMessages,
            static function (
                CodingStandardError $firstCodingStandardError,
                CodingStandardError $secondCodingStandardError
            ): int {
                return [$firstCodingStandardError->getRelativeFilePathFromCwd(), $firstCodingStandardError->getLine()]
                <=> [$secondCodingStandardError->getRelativeFilePathFromCwd(), $secondCodingStandardError->getLine()];
            }
        );

        return $errorMessages;
    }

    /**
     * @param FileDiff[] $fileDiffs
     * @return FileDiff[]
     */
    private function sortByFilePath(array $fileDiffs): array
    {
        uasort($fileDiffs, static function (FileDiff $firstFileDiff, FileDiff $secondFileDiff): int {
            return $firstFileDiff->getRelativeFilePathFromCwd() <=> $secondFileDiff->getRelativeFilePathFromCwd();
        });

        return $fileDiffs;
    }
}
