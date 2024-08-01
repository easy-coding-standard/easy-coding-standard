<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Style;

use ECSPrefix202408\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202408\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix202408\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix202408\Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
final class EasyCodingStandardStyle extends SymfonyStyle
{
    /**
     * To fit in Linux/Windows terminal windows to prevent overflow.
     *
     * @var int
     */
    private const BULGARIAN_CONSTANT = 8;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Terminal
     */
    private $terminal;
    public function __construct(InputInterface $input, OutputInterface $output, Terminal $terminal)
    {
        $this->terminal = $terminal;
        parent::__construct($input, $output);
    }
    /**
     * @param CodingStandardError[] $codingStandardErrors
     */
    public function printErrors(array $codingStandardErrors) : void
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
    private function separator() : void
    {
        $separator = \str_repeat('-', $this->getTerminalWidth());
        $this->writeln(' ' . $separator);
    }
    private function createMessageFromFileError(CodingStandardError $codingStandardError) : string
    {
        $message = \sprintf('%s%s Reported by: "%s"', $codingStandardError->getMessage(), \PHP_EOL . \PHP_EOL, $codingStandardError->getCheckerClass());
        $message = $this->clearCrLfFromMessage($message);
        return $this->wrapMessageSoItFitsTheColumnWidth($message);
    }
    private function getTerminalWidth() : int
    {
        return $this->terminal->getWidth() - self::BULGARIAN_CONSTANT;
    }
    /**
     * This prevents message override in Windows system.
     */
    private function clearCrLfFromMessage(string $message) : string
    {
        return \str_replace("\r", '', $message);
    }
    private function wrapMessageSoItFitsTheColumnWidth(string $message) : string
    {
        return \wordwrap($message, $this->getTerminalWidth(), \PHP_EOL);
    }
}
