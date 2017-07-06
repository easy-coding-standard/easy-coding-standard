<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Style;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\Error\Error;

final class EasyCodingStandardStyle
{
    /**
     * @var int
     */
    private const LINE_COLUMN_WIDTH = 4;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var bool
     */
    private $hasProgressBarStarted = false;

    /**
     * @var Terminal
     */
    private $terminal;

    public function __construct(SymfonyStyleFactory $symfonyStyleFactory, Terminal $terminal)
    {
        $this->symfonyStyle = $symfonyStyleFactory->create();
        $this->terminal = $terminal;
    }

    public function title(string $message): void
    {
        $this->symfonyStyle->title($message);
    }

    public function section(string $message): void
    {
        $this->symfonyStyle->section($message);
    }

    public function text(string $message): void
    {
        $this->symfonyStyle->text($message);
    }

    public function success(string $message): void
    {
        $this->symfonyStyle->success($message);
    }

    public function error(string $message): void
    {
        $this->symfonyStyle->error($message);
    }

    public function newLine(): void
    {
        $this->symfonyStyle->newLine();
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

    public function startProgressBar(int $max): void
    {
        $this->hasProgressBarStarted = true;
        $this->symfonyStyle->progressStart($max);
    }

    public function advanceProgressBar(): void
    {
        if (! $this->hasProgressBarStarted) {
            return;
        }

        $this->symfonyStyle->progressAdvance();
    }

    /**
     * @param string[] $headers
     * @param mixed[] $rows
     */
    private function table(array $headers, array $rows): void
    {
        $style = clone Table::getStyleDefinition('symfony-style-guide');
        $style->setCellHeaderFormat('%s');

        $rows = $this->wrapTextSoItFitsTheColumnWidth($rows);

        $table = new Table($this->symfonyStyle);
        $table->setColumnWidths([self::LINE_COLUMN_WIDTH, $this->countMessageColumnWidth()]);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->setStyle($style);

        $table->render();
        $this->symfonyStyle->newLine();
    }

    /**
     * @param Error[] $errors
     * @return mixed[]
     */
    private function buildFileTableRowsFromErrors(array $errors): array
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
            'message' => $this->wrapMessageToStyle($message, $error->isFixable()),
        ];
    }

    private function wrapMessageToStyle(string $message, bool $isFixable): string
    {
        if ($isFixable) {
            return sprintf('<fg=black;bg=green>%s</>', $message);
        }

        return sprintf('<fg=black;bg=red>%s</>', $message);
    }

    /**
     * @param mixed[] $rows
     * @return mixed[]
     */
    private function wrapTextSoItFitsTheColumnWidth(array $rows): array
    {
        foreach ($rows as $id => $row) {
            $rows[$id]['message'] = wordwrap($row['message'], $this->countMessageColumnWidth());
        }

        return $rows;
    }

    private function countMessageColumnWidth(): int
    {
        return $this->terminal->getWidth() - self::LINE_COLUMN_WIDTH - 7;
    }
}
