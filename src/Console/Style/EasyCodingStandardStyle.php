<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Style;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\Error\Error;

final class EasyCodingStandardStyle extends SymfonyStyle
{
    /**
     * @var int
     */
    private const LINE_COLUMN_WIDTH = 4;

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
        /** @var Error[] $errors */
        foreach ($errors as $file => $fileErrors) {
            $this->table(['Line', $file], $this->buildFileTableRowsFromErrors($fileErrors));
        }
    }

    /**
     * @param Error[] $errors
     * @return string[]
     */
    public function buildFileTableRowsFromErrors(array $errors): array
    {
        $rows = [];
        foreach ($errors as $error) {
            $message = $error->getMessage() . PHP_EOL . '(' . $error->getSourceClass() . ')';
            $message = $this->clearCrLfFromMessage($message);
            $message = $this->wrapMessageSoItFitsTheColumnWidth($message);

            $rows[] = $this->buildRow($error, $message);
        }

        return $rows;
    }

    /**
     * @param string[] $headers
     * @param mixed[] $rows
     */
    public function table(array $headers, array $rows): void
    {
        $style = clone Table::getStyleDefinition('symfony-style-guide');
        $style->setCellHeaderFormat('%s');

        $table = new Table($this);
        $table->setColumnWidths([self::LINE_COLUMN_WIDTH, $this->countMessageColumnWidth()]);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->setStyle($style);

        $table->render();
        $this->newLine();
    }

    /**
     * @param string[] $elements
     */
    public function listing(array $elements): void
    {
        $elements = array_map(function ($element) {
            return sprintf(' - %s', $element);
        }, $elements);
        $this->writeln($elements);
        $this->newLine();
    }

    /**
     * @return string[]
     */
    private function buildRow(Error $error, string $message): array
    {
        return [
            'line' => (string) $error->getLine(),
            'message' => $message,
        ];
    }

    private function wrapMessageSoItFitsTheColumnWidth(string $message): string
    {
        return wordwrap($message, $this->countMessageColumnWidth(), PHP_EOL);
    }

    private function countMessageColumnWidth(): int
    {
        return $this->terminal->getWidth() - self::LINE_COLUMN_WIDTH - self::BULGARIAN_CONSTANT;
    }

    /**
     * This prevents message override in Windows system.
     */
    private function clearCrLfFromMessage(string $message): string
    {
        return str_replace("\r", '', $message);
    }
}
