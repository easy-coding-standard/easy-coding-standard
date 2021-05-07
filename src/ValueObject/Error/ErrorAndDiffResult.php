<?php

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
    /**
     * @return int
     */
    public function getErrorCount()
    {
        return \count($this->codingStandardErrors) + \count($this->systemErrors);
    }
    /**
     * @return int
     */
    public function getFileDiffsCount()
    {
        return \count($this->fileDiffs);
    }
    /**
     * @return mixed[]
     */
    public function getErrors()
    {
        return $this->codingStandardErrors;
    }
    /**
     * @return mixed[]
     */
    public function getSystemErrors()
    {
        return $this->systemErrors;
    }
    /**
     * @return mixed[]
     */
    public function getFileDiffs()
    {
        return $this->fileDiffs;
    }
    /**
     * @param CodingStandardError[] $errorMessages
     * @return mixed[]
     */
    private function sortByFileAndLine(array $errorMessages)
    {
        \usort($errorMessages, static function (\Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError $firstCodingStandardError, \Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError $secondCodingStandardError) : int {
            $battleShipcompare = function ($left, $right) {
                if ($left === $right) {
                    return 0;
                }
                return $left < $right ? -1 : 1;
            };
            return $battleShipcompare([$firstCodingStandardError->getRelativeFilePathFromCwd(), $firstCodingStandardError->getLine()], [$secondCodingStandardError->getRelativeFilePathFromCwd(), $secondCodingStandardError->getLine()]);
        });
        return $errorMessages;
    }
    /**
     * @param FileDiff[] $fileDiffs
     * @return mixed[]
     */
    private function sortByFilePath(array $fileDiffs)
    {
        \uasort($fileDiffs, static function (\Symplify\EasyCodingStandard\ValueObject\Error\FileDiff $firstFileDiff, \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff $secondFileDiff) : int {
            $battleShipcompare = function ($left, $right) {
                if ($left === $right) {
                    return 0;
                }
                return $left < $right ? -1 : 1;
            };
            return $battleShipcompare($firstFileDiff->getRelativeFilePathFromCwd(), $secondFileDiff->getRelativeFilePathFromCwd());
        });
        return $fileDiffs;
    }
}
