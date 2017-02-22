<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Report;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Report\Error\Error;

final class ErrorCollector
{
    /**
     * @var int
     */
    private $errorCount = 0;

    /**
     * @var int
     */
    private $fixableErrorCount = 0;

    /**
     * @var Error[][]
     */
    private $errors = [];

    /**
     * @var ErrorSorter
     */
    private $errorMessageSorter;

    public function __construct(ErrorSorter $errorMessageSorter)
    {
        $this->errorMessageSorter = $errorMessageSorter;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    public function getFixableErrorCount(): int
    {
        return $this->fixableErrorCount;
    }

    /**
     * @return Error[][]
     */
    public function getErrors(): array
    {
        return $this->errorMessageSorter->sortByFileAndLine($this->errors);
    }

    public function getUnfixableErrorCount(): int
    {
        return count($this->getUnfixableErrorMessages());
    }

    /**
     * @return Error[][]
     */
    public function getUnfixableErrorMessages(): array
    {
        $unfixableErrorMessages = [];
        foreach ($this->getErrors() as $file => $errorMessagesForFile) {
            $unfixableErrorMessagesForFile = $this->filterUnfixableErrorMessagesForFile($errorMessagesForFile);
            if (count($unfixableErrorMessagesForFile)) {
                $unfixableErrorMessages[$file] = $unfixableErrorMessagesForFile;
            }
        }

        return $unfixableErrorMessages;
    }

    public function addErrorMessage(
        string $filePath,
        string $message,
        int $line,
        string $sourceClass,
        bool $isFixable = true
    ): void {
        $this->errorCount++;

        if ($isFixable) {
            $this->fixableErrorCount++;
        }

        $this->errors[$filePath][] = new Error($line, $message, $this->normalizeSniffClass($sourceClass), $isFixable);
    }

    /**
     * @param Error[] $errorMessagesForFile
     * @return Error[]
     */
    private function filterUnfixableErrorMessagesForFile(array $errorMessagesForFile): array
    {
        $unfixableErrorMessages = [];
        foreach ($errorMessagesForFile as $errorMessage) {
            if ($errorMessage->isFixable()) {
                continue;
            }

            $unfixableErrorMessages[] = $errorMessage;
        }

        return $unfixableErrorMessages;
    }

    private function normalizeSniffClass(string $sourceClass): string
    {
        if (class_exists($sourceClass, false)) {
            return $sourceClass;
        }

        $trace = debug_backtrace(0, 6);

        if ($this->isSniffClass($trace[5]['class'])) {
            return $trace[5]['class'];
        }

        return $trace[4]['class'];
    }

    private function isSniffClass(string $class): bool
    {
        return is_a($class, Sniff::class, true);
    }
}
