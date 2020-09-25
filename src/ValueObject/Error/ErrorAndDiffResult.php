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
     * @param CodingStandardError[] $codingStandardErrors
     * @param FileDiff[] $fileDiffs
     */
    public function __construct(array $codingStandardErrors, array $fileDiffs)
    {
        $this->codingStandardErrors = $this->sortByFileAndLine($codingStandardErrors);
        $this->fileDiffs = $this->sortByFilePath($fileDiffs);
    }

    public function getErrorCount(): int
    {
        return count($this->codingStandardErrors);
    }

    public function getFileDiffsCount(): int
    {
        return count($this->getFileDiffs());
    }

    /**
     * @return CodingStandardError[]
     */
    public function getErrors(): array
    {
        return $this->codingStandardErrors;
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
