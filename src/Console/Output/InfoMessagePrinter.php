<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\Error\Error;
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

    public function __construct(
        ErrorCollector $errorDataCollector,
        EasyCodingStandardStyle $easyCodingStandardStyle
    ) {
        $this->errorDataCollector = $errorDataCollector;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    public function hasSomeErrorMessages(): bool
    {
        return (bool) $this->errorDataCollector->getErrorCount();
    }

    /**
     * @param bool $isFixer
     * @param string[] $ignoredErrors
     */
    public function printFoundErrorsStatus(bool $isFixer, array $ignoredErrors): void
    {
        $this->easyCodingStandardStyle->newLine();

        $errorMessages = $this->getRelevantErrorMessages($isFixer, $ignoredErrors);

        /** @var Error[] $errors */
        foreach ($errorMessages as $file => $errors) {
            $rows = [];
            foreach ($errors as $error) {
                $message = $error->getMessage() . PHP_EOL . '(' . $error->getSourceClass() . ')';

                $rows[] = [
                    'line' => $this->wrapMessageToStyle((string) $error->getLine(), $error->isFixable()),
                    'message' => $this->wrapMessageToStyle($message, $error->isFixable())
                ];
            }

            $this->easyCodingStandardStyle->table(['Line', $file], $rows);
        }

        $this->easyCodingStandardStyle->error($this->buildErrorMessage($isFixer));
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

    private function wrapMessageToStyle(string $message, bool $isFixable): string
    {
        if ($isFixable) {
            return sprintf('<fg=black;bg=green>%s</>', $message);
        }

        return sprintf('<fg=black;bg=red>%s</>', $message);
    }

    /**
     * @return Error[][]
     */
    private function getRelevantErrorMessages(bool $isFixer, array $ignoredErrors): array
    {
        if ($isFixer) {
            return $this->errorDataCollector->getUnfixableErrors();
        }

        // @todo: filter out those ignored!
        dump($ignoredErrors);
        dump($this->errorDataCollector->getErrors());
        die;

        return $this->errorDataCollector->getErrors();
    }

    private function getRelevantErrorCount(bool $isFixer): int
    {
        if ($isFixer) {
            return $this->errorDataCollector->getUnfixableErrorCount();
        }

        return $this->errorDataCollector->getErrorCount();
    }
}
