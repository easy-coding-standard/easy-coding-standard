<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Report;

use PHP_CodeSniffer\Sniffs\Sniff;

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
     * @var array[]
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

    public function getErrorCount() : int
    {
        return $this->errorCount;
    }

    public function getFixableErrorCount() : int
    {
        return $this->fixableErrorCount;
    }

    public function getErrorMessages() : array
    {
        return $this->errorMessageSorter->sortByFileAndLine($this->errorMessages);
    }

    public function getUnfixableErrorMessages() : array
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

        $this->errorMessages[$filePath][] = [
            'line' => $line,
            'message' => $this->applyDataToMessage($message, $data),
            'sourceClass' => $this->normalizeSniffClass($sourceClass),
            'isFixable'  => $isFixable
        ];
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

    private function filterUnfixableErrorMessagesForFile(array $errorMessagesForFile): array
    {
        $unfixableErrorMessages = [];
        foreach ($errorMessagesForFile as $errorMessage) {
            if ($errorMessage['isFixable']) {
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
        if (is_a($trace[5]['class'], Sniff::class)) {
            return $trace[5]['class'];
        }

        return $trace[4]['class'];
    }
}
