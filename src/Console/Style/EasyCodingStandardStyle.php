<?php

namespace Symplify\EasyCodingStandard\Console\Style;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class EasyCodingStandardStyle extends SymfonyStyle
{
    /**
     * To fit in Linux/Windows terminal windows to prevent overflow.
     *
     * @var int
     */
    const BULGARIAN_CONSTANT = 8;

    /**
     * @var Terminal
     */
    private $terminal;

    public function __construct(InputInterface $input, OutputInterface $output, Terminal $terminal)
    {
        parent::__construct($input, $output);

        $this->terminal = $terminal;
    }

    /**
     * @param CodingStandardError[] $codingStandardErrors
     * @return void
     */
    public function printErrors(array $codingStandardErrors)
    {
        /** @var CodingStandardError $codingStandardError */
        foreach ($codingStandardErrors as $codingStandardError) {
            $this->separator();

            $this->writeln(' ' . $codingStandardError->getFileWithLine());

            $this->separator();

            $message = $this->createMessageFromFileError($codingStandardError);
            $this->writeln(' ' . $message);

            $this->separator();

            $this->newLine();
        }
    }

    /**
     * @return void
     */
    public function enableDebugProgressBar()
    {
        $privatesAccessor = new PrivatesAccessor();
        $progressBar = $privatesAccessor->getPrivateProperty($this, 'progressBar');

        $privatesCaller = new PrivatesCaller();
        $privatesCaller->callPrivateMethod($progressBar, 'setRealFormat', ['debug']);
    }

    /**
     * @return void
     */
    private function separator()
    {
        $separator = str_repeat('-', $this->getTerminalWidth());
        $this->writeln(' ' . $separator);
    }

    /**
     * @return string
     */
    private function createMessageFromFileError(CodingStandardError $codingStandardError)
    {
        $message = sprintf(
            '%s%s Reported by: "%s"',
            $codingStandardError->getMessage(),
            PHP_EOL . PHP_EOL,
            $codingStandardError->getCheckerClass()
        );
        $message = $this->clearCrLfFromMessage($message);

        return $this->wrapMessageSoItFitsTheColumnWidth($message);
    }

    /**
     * @return int
     */
    private function getTerminalWidth()
    {
        return $this->terminal->getWidth() - self::BULGARIAN_CONSTANT;
    }

    /**
     * This prevents message override in Windows system.
     * @param string $message
     * @return string
     */
    private function clearCrLfFromMessage($message)
    {
        $message = (string) $message;
        return str_replace("\r", '', $message);
    }

    /**
     * @param string $message
     * @return string
     */
    private function wrapMessageSoItFitsTheColumnWidth($message)
    {
        $message = (string) $message;
        return wordwrap($message, $this->getTerminalWidth(), PHP_EOL);
    }
}
