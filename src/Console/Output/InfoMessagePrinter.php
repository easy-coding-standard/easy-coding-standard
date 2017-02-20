<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Report\Error\Error;
use Symplify\EasyCodingStandard\Report\ErrorDataCollector;

final class InfoMessagePrinter
{
    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    public function __construct(
        ErrorDataCollector $errorDataCollector,
        EasyCodingStandardStyle $easyCodingStandardStyle
    ) {
        $this->errorDataCollector = $errorDataCollector;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    public function hasSomeErrorMessages() : bool
    {
        if ($this->errorDataCollector->getErrorCount()) {
            return true;
        }

        return false;
    }

    public function printFoundErrorsStatus(bool $isFixer) : void
    {
        $errorMessages = $isFixer
            ? $this->errorDataCollector->getUnfixableErrorMessages()
            : $this->errorDataCollector->getErrorMessages();

        foreach ($errorMessages as $file => $errors) {
            $rows = [];
            foreach ($errors as $error) {
                $message = $error[Error::MESSAGE] . PHP_EOL . '(' . $error[Error::SOURCE_CLASS] . ')';

                $rows[] = [
                    Error::LINE => $this->wrapMessageToStyle((string) $error[Error::LINE], $error[Error::IS_FIXABLE]),
                    Error::MESSAGE => $this->wrapMessageToStyle($message, $error[Error::IS_FIXABLE])
                ];
            }

            $this->easyCodingStandardStyle->table([Error::LINE, $file], $rows);
        }

        $this->easyCodingStandardStyle->error($this->buildErrorMessage($isFixer));
    }

    private function buildErrorMessage(bool $isFixer): string
    {
        $errorCount = $isFixer
            ? $this->errorDataCollector->getUnfixableErrorCount()
            : $this->errorDataCollector->getErrorCount();

        $message = sprintf(
            $errorCount === 1 ? 'Found %d error.' : 'Found %d errors.',
            $errorCount
        );

        if ($this->errorDataCollector->getFixableErrorCount()) {
            if ($errorCount === $this->errorDataCollector->getFixableErrorCount()) {
                $howMany = 'All';
            } else {
                $howMany =$this->errorDataCollector->getFixableErrorCount();
            }

            $message .= sprintf(' %s of them are fixable!', $howMany);
            $message .= ' Just add "--fix" to console command and rerun to apply.';
        }

        return $message;
    }

    private function wrapMessageToStyle(string $message, bool $isFixable) : string
    {
        if ($isFixable) {
            return sprintf('<fg=black;bg=green>%s</>', $message);
        }

        return sprintf('<fg=black;bg=red>%s</>', $message);
    }
}
