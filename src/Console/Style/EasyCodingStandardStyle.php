<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Style;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\Error\Error;

final class EasyCodingStandardStyle extends SymfonyStyle
{
    /**
     * To fit in Linux/Windows terminal windows to prevent overflow.
     * @var int
     */
    private const BULGARIAN_CONSTANT = 8;

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
     * @param Error[][] $errors
     */
    public function printErrors(array $errors): void
    {
        foreach ($errors as $fileErrors) {
            /** @var Error $fileError */
            foreach ($fileErrors as $fileError) {
                $this->separator();

                $fileLineLink = $fileError->getFileInfo()->getRelativeFilePathFromDirectory(
                    getcwd()
                ) . ':' . $fileError->getLine();
                $this->writeln(' ' . $fileLineLink);

                $this->separator();

                $message = $this->createMessageFromFileError($fileError);
                $this->writeln(' ' . $message);

                $this->separator();

                $this->newLine();
            }
        }
    }

    public function fixableError(string $message): void
    {
        $this->block($message, 'WARNING', 'fg=black;bg=yellow', ' ', true);
    }

    private function separator(): void
    {
        $separator = str_repeat('-', $this->getTerminalWidth());
        $this->writeln(' ' . $separator);
    }

    private function createMessageFromFileError(Error $fileError): string
    {
        $message = sprintf('%s%s (%s)', $fileError->getMessage(), PHP_EOL, $fileError->getSourceClass());
        $message = $this->clearCrLfFromMessage($message);

        return $this->wrapMessageSoItFitsTheColumnWidth($message);
    }

    private function getTerminalWidth(): int
    {
        return $this->terminal->getWidth() - self::BULGARIAN_CONSTANT;
    }

    /**
     * This prevents message override in Windows system.
     */
    private function clearCrLfFromMessage(string $message): string
    {
        return str_replace("\r", '', $message);
    }

    private function wrapMessageSoItFitsTheColumnWidth(string $message): string
    {
        return wordwrap($message, $this->getTerminalWidth(), PHP_EOL);
    }
}
