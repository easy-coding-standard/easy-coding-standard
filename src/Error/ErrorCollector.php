<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Arrays;

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

    public function __construct(ErrorSorter $errorMessageSorter)
    {
        $this->errorMessageSorter = $errorMessageSorter;
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
    public function getErrors(): array
    {
        return $this->errorMessageSorter->sortByFileAndLine($this->fixableErrors) + $this->getUnfixableErrors();
    }

    /**
     * @return Error[][]
     */
    public function getUnfixableErrors(): array
    {
        return $this->errorMessageSorter->sortByFileAndLine($this->unfixableErrors);
    }
}
