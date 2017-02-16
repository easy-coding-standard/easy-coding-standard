<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
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
        foreach ($this->errorDataCollector->getErrorMessages() as $file => $errors) {
            $rows = [];
            foreach ($errors as $error) {
                $rows[] = [
                    'line' => $this->wrapMessageToStyle((string) $error['line'], $error['isFixable']),
                    'message' => $this->wrapMessageToStyle($error['message'], $error['isFixable'])
                ];
            }

            $this->easyCodingStandardStyle->table(['Line', $file], $rows);
        }

        $this->easyCodingStandardStyle->error($this->buildErrorMessage());

        // code sniffer
//        $this->phpCodeSnifferInfoMessagePrinter->printFoundErrorsStatus($isFixer);
//        $diffs = $this->diffDataCollector->getDiffs();
//        $this->printDiffs($diffs);
    }

    private function buildErrorMessage(): string
    {
        $errorCount = $this->errorDataCollector->getErrorCount();
        $message = sprintf(
            $errorCount === 1 ? 'Found %d error.' : 'Found %d errors.',
            $errorCount
        );

        if ($this->errorDataCollector->getFixableErrorCount()) {
            if ($errorCount === $this->errorDataCollector->getFixableErrorCount()) {
                $howMany = ' All';
            } else {
                $howMany =$this->errorDataCollector->getFixableErrorCount();
            }

            $message .= sprintf('%s of them are fixable!', $howMany);
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
