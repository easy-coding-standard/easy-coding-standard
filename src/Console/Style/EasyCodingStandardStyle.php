<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Style;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;

final class EasyCodingStandardStyle
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var bool
     */
    private $progressStarted = false;

    public function __construct(SymfonyStyleFactory $symfonyStyleFactory)
    {
        $this->symfonyStyle = $symfonyStyleFactory->create();
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

    public function table(array $headers, array $rows): void
    {
        $style = clone Table::getStyleDefinition('symfony-style-guide');
        $style->setCellHeaderFormat('%s');

        $table = new Table($this->symfonyStyle);
        $table->setColumnWidths([4, 110]);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->setStyle($style);

        $table->render();
        $this->symfonyStyle->newLine();
    }

    public function progressStart(int $max): void
    {
        $this->progressStarted = true;
        $this->symfonyStyle->progressStart($max);
    }

    public function progressAdvance(): void
    {
        if (! $this->progressStarted) {
            return;
        }

        $this->symfonyStyle->progressAdvance();
    }
}
