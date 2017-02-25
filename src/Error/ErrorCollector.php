<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

/**
 * @todo Maybe use fixableErrors and unfixableErrors properties.
 * Better usability, e.g.: "only show unfixableErrors", to display errors,
 * that need to be fixed manually. Also could drop that weird filter API.
 */
final class ErrorCollector
{
    /**
     * @var Error[][]
     */
    private $errors = [];

    /**
     * @var ErrorSorter
     */
    private $errorMessageSorter;

    /**
     * @var ErrorFilter
     */
    private $errorFilter;

    public function __construct(ErrorSorter $errorMessageSorter, ErrorFilter $errorFilter)
    {
        $this->errorMessageSorter = $errorMessageSorter;
        $this->errorFilter = $errorFilter;
    }

    public function addErrorMessage(
        string $filePath,
        int $line,
        string $message,
        string $sourceClass,
        bool $isFixable
    ): void {
        $this->errors[$filePath][] = new Error($line, $message, $sourceClass, $isFixable);
    }

    public function getErrorCount(): int
    {
        $errorCount = 0;
        foreach ($this->getErrors() as $errorsForFile) {
            $errorCount += count($errorsForFile);
        }

        return $errorCount;
    }

    public function getFixableErrorCount(): int
    {
        return $this->getErrorCount() - $this->getUnfixableErrorCount();
    }

    public function getUnfixableErrorCount(): int
    {
        return count($this->getUnfixableErrors());
    }

    /**
     * @return Error[][]
     */
    public function getErrors(): array
    {
        $errors = $this->errorMessageSorter->sortByFileAndLine($this->errors);

        return $this->errorFilter->filterOutIgnoredErrors($errors);
    }

    /**
     * @return Error[][]
     */
    public function getUnfixableErrors(): array
    {
        return $this->errorFilter->filterOutFixableErrors($this->getErrors());
    }
}
