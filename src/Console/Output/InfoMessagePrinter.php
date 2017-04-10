<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorCollector;

final class InfoMessagePrinter
{
    /**
     * @var ErrorCollector
     */
    private $errorDataCollector;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    public function __construct(ErrorCollector $errorDataCollector, EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        $this->errorDataCollector = $errorDataCollector;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    public function hasSomeErrorMessages(): bool
    {
        return (bool) $this->errorDataCollector->getErrorCount();
    }

    public function printFoundErrorsStatus(bool $isFixer): void
    {
        $this->easyCodingStandardStyle->newLine();

        $errorMessages = $this->getRelevantErrors($isFixer);

        /** @var Error[] $errors */
        foreach ($errorMessages as $file => $errors) {
            $rows = $this->buildFileTable($errors);
            $this->easyCodingStandardStyle->table(['Line', $file], $rows);
        }

        $errorCount = $this->getRelevantErrorCount($isFixer);
        if ($errorCount === 0 && $isFixer === true) {
            $this->easyCodingStandardStyle->success($this->buildErrorMessage($isFixer));
        } else {
            $this->easyCodingStandardStyle->error($this->buildErrorMessage($isFixer));
        }
    }

    private function buildErrorMessage(bool $isFixer): string
    {
        $errorCount = $this->getRelevantErrorCount($isFixer);

        $message = sprintf(
            $errorCount === 1 ? 'Found %d error.' : 'Found %d errors.',
            $errorCount
        );

        if ($isFixer === false && $this->errorDataCollector->getFixableErrorCount()) {
            if ($errorCount === $this->errorDataCollector->getFixableErrorCount()) {
                $howMany = 'All';
            } else {
                $howMany = $this->errorDataCollector->getFixableErrorCount();
            }

            $message .= sprintf(' %s of them are fixable!', $howMany);
            $message .= ' Just add "--fix" to console command and rerun to apply.';
        }

        return $message;
    }

    /**
     * @return Error[][]
     */
    private function getRelevantErrors(bool $isFixer): array
    {
        if ($isFixer) {
            $errors = $this->errorDataCollector->getUnfixableErrors();
        } else {
            $errors = $this->errorDataCollector->getAllErrors();
        }

        return $errors;
    }

    private function getRelevantErrorCount(bool $isFixer): int
    {
        $errors = $this->getRelevantErrors($isFixer);

        $errorCount = 0;
        foreach ($errors as $errorsForFile) {
            $errorCount += count($errorsForFile);
        }

        return $errorCount;
    }

    /**
     * @param Error[] $errors
     * @return mixed[]
     */
    private function buildFileTable(array $errors): array
    {
        $rows = [];
        foreach ($errors as $error) {
            $message = $error->getMessage() . PHP_EOL . '(' . $error->getSourceClass() . ')';
            $rows[] = $this->buildRow($error, $message);
        }

        return $rows;
    }

    /**
     * @return string[]
     */
    private function buildRow(Error $error, string $message): array
    {
        return [
            'line' => $this->wrapMessageToStyle((string) $error->getLine(), $error->isFixable()),
            'message' => $this->wrapMessageToStyle($message, $error->isFixable())
        ];
    }

    private function wrapMessageToStyle(string $message, bool $isFixable): string
    {
        if ($isFixable) {
            return sprintf('<fg=black;bg=green>%s</>', $message);
        }

        return sprintf('<fg=black;bg=red>%s</>', $message);
    }
}
