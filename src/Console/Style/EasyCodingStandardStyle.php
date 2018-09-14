<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Style;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\Error\Error;
use function Safe\sprintf;

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

    /**
     * @var Table
     */
    private $genericTable;

    public function __construct(InputInterface $input, OutputInterface $output, Terminal $terminal)
    {
        parent::__construct($input, $output);
        $this->terminal = $terminal;

        $table = new Table($this);
        $style = Table::getStyleDefinition('symfony-style-guide');
        /** make headlines green manually as in parent @see SymfonyStyle::table() */
        $style->setCellHeaderFormat('<info>%s</info>');
        $table->setStyle($style);

        $this->genericTable = $table;
    }

    /**
     * @param Error[][] $errors
     */
    public function printErrors(array $errors): void
    {
        /** @var Error[][] $errors */
        foreach ($errors as $file => $fileErrors) {
            $headers = ['Line', $file];
            $rows = $this->buildFileTableRowsFromErrors($fileErrors);
            $this->tableWithColumnWidths($headers, $rows, [
                self::LINE_COLUMN_WIDTH, $this->countMessageColumnWidth(self::LINE_COLUMN_WIDTH),
            ]);
        }
    }

    /**
     * @param Error[] $errors
     * @return string[][]
     */
    public function buildFileTableRowsFromErrors(array $errors): array
    {
        $rows = [];
        foreach ($errors as $error) {
            $message = sprintf('%s%s(%s)', $error->getMessage(), PHP_EOL, $error->getSourceClass());
            $message = $this->clearCrLfFromMessage($message);
            $message = $this->wrapMessageSoItFitsTheColumnWidth($message);

            $rows[] = $this->buildRow($error, $message);
        }

        return $rows;
    }

    /**
     * @param string[] $elements
     */
    public function listing(array $elements): void
    {
        $elements = array_map(function ($element): string {
            return sprintf(' - %s', $element);
        }, $elements);
        $this->writeln($elements);
        $this->newLine();
    }

    public function fixableError(string $message): void
    {
        $this->block($message, 'WARNING', 'fg=black;bg=yellow', ' ', true);
    }

    /**
     * @param string[] $headers
     * @param mixed[] $rows
     * @param int[] $columnWidths
     */
    private function tableWithColumnWidths(array $headers, array $rows, array $columnWidths): void
    {
        $this->genericTable->setHeaders($headers);
        $this->genericTable->setRows($rows);

        $this->genericTable->setColumnWidths($columnWidths);
        $this->genericTable->render();

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
        return wordwrap($message, $this->countMessageColumnWidth(self::LINE_COLUMN_WIDTH), PHP_EOL);
    }

    private function countMessageColumnWidth(int $otherColumnWidth): int
    {
        return $this->terminal->getWidth() - $otherColumnWidth - self::BULGARIAN_CONSTANT;
    }

    /**
     * This prevents message override in Windows system.
     */
    private function clearCrLfFromMessage(string $message): string
    {
        return str_replace("\r", '', $message);
    }
}
