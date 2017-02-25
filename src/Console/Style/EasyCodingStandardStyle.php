<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Style;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;

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
    private $progressBarStarted = false;

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
     * @param string[] $headers
     * @param mixed[] $rows
     */
    public function table(array $headers, array $rows): void
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

    public function progressBarStart(int $max): void
    {
        $this->progressBarStarted = true;
        $this->symfonyStyle->progressStart($max);
    }

    public function progressBarAdvance(): void
    {
        if (! $this->progressBarStarted) {
            return;
        }

        $this->symfonyStyle->progressAdvance();
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
