<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symfony\Component\Console\Style\StyleInterface;
use Symplify\SniffRunner\Report\ErrorDataCollector;

final class InfoMessagePrinter
{
    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    /**
     * @var StyleInterface
     */
    private $output;

    public function __construct(
        ErrorDataCollector $errorDataCollector,
        StyleInterface $output
    ) {
        $this->errorDataCollector = $errorDataCollector;
        $this->output = $output;
    }

    public function hasSomeErrorMessages() : bool
    {
        if ($this->errorDataCollector->getErrorCount()) {
            return true;
        }

        return false;
    }

    public function printFoundErrorsStatus(bool $isFixer)
    {
        $this->output->title(sprintf(
            'Found %d errors',
            $this->errorDataCollector->getErrorCount()
        ));
        foreach ($this->errorDataCollector->getErrorMessages() as $file => $errors) {

            $this->output->newLine();

            $this->output->section($file);

            foreach ($errors as $error) {
                $message = 'Line ' . $error['line'] . ' - ' . $error['message'];
                if ($error['isFixable']) {
                    $this->output->caution($message);
                } else {
                    $this->output->warning($message);
                }
            }
        }
        die;

        // @todo: combine to onw printer!!!!

        // code sniffer
        $this->phpCodeSnifferInfoMessagePrinter->printFoundErrorsStatus($isFixer);

//        $diffs = $this->diffDataCollector->getDiffs();
//        $this->printDiffs($diffs);
    }
}
