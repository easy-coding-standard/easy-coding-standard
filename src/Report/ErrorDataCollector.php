<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Report;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Report\Error\Error;

final class ErrorDataCollector
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
     * @var Error[]
     */
    private $errorMessages = [];

    /**
     * @var ErrorMessageSorter
     */
    private $errorMessageSorter;

    public function __construct(ErrorMessageSorter $errorMessageSorter)
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
    public function getErrorMessages(): array
    {
        return $this->errorMessageSorter->sortByFileAndLine($this->errorMessages);
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
        foreach ($this->getErrorMessages() as $file => $errorMessagesForFile) {
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
        array $data = [],
        bool $isFixable = false
    ): void {
        $this->errorCount++;

        if ($isFixable) {
            $this->fixableErrorCount++;
        }

        $error = new Error(
            $line,
            $this->applyDataToMessage($message, $data),
            $this->normalizeSniffClass($sourceClass),
            $isFixable
        );

        $this->errorMessages[$filePath][] = $error;
    }

    /**
     * For back compatibility with PHP_CodeSniffer 3.0.
     */
    private function applyDataToMessage(string $message, array $data): string
    {
        if (count($data)) {
            $message = vsprintf($message, $data);
        }

        return $message;
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
